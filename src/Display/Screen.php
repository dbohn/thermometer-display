<?php

namespace Thermometer\Display;

use FFI;
use WyriHaximus\React\Mutex\Lock;
use WyriHaximus\React\Mutex\Memory;

class Screen
{

    protected $libraryPath = './epaper/src/libepaper.h';
    protected $ffi = null;
    protected $buffer;
    protected $width;
    protected $height;

    protected $mutex;

    protected const MUTEX_KEY = 'SCREEN_MUTEX';

    public function __construct($width = 176, $height = 264)
    {
        $this->ffi = FFI::load($this->libraryPath);
        $this->ffi->DEV_Module_Init();
        $this->ffi->EPD_2IN7_Init();

        $bufferType = FFI::arrayType(FFI::type("uint8_t"), [$width * $height / 8]);

        $this->buffer = FFI::new($bufferType);
        $this->width = $width;
        $this->height = $height;

        $this->mutex = new Memory();
    }

    public function __destruct()
    {
        $this->ffi->DEV_Module_Exit();
    }

    public function clear()
    {
        $this->ffi->EPD_2IN7_Clear();
    }

    public function draw(string $imageData)
    {
        $this->mutex->acquire(self::MUTEX_KEY)->then(function ($lock) use ($imageData) {
            // Check if we were able to acquire the lock
            if (!($lock instanceof Lock)) {
                return;
            }

            // Copy image to framebuffer and redraw screen
            FFI::memcpy($this->buffer, $imageData, min($this->bufferSize(), strlen($imageData)));
            $this->ffi->EPD_2IN7_Display($this->buffer);

            $this->mutex->release($lock);
        });
    }

    public function sleep()
    {
        $this->ffi->EPD_2IN7_Sleep();
    }

    public function wakeup()
    {
        $this->ffi->EPD_2IN7_Init();
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    protected function bufferSize()
    {
        return $this->width * $this->height / 8;
    }
}
