<?php

namespace Thermometer\Display;

use FFI;

class Screen
{

    protected $libraryPath = './epaper/src/libepaper.h';
    protected $ffi = null;
    protected $buffer;
    protected $width;
    protected $height;

    public function __construct($width = 176, $height = 264)
    {
        $this->ffi = FFI::load($this->libraryPath);
        echo "Initializing" . PHP_EOL;
        $this->ffi->DEV_Module_Init();
        $this->ffi->EPD_2IN7_Init();

        $bufferType = FFI::arrayType(FFI::type("uint8_t"), [$width * $height / 8]);

        $this->buffer = FFI::new($bufferType);
        $this->width = $width;
        $this->height = $height;
    }

    public function clear()
    {
        $this->ffi->EPD_2IN7_Clear();
    }

    public function draw(string $imageData)
    {
        FFI::memcpy($this->buffer, $imageData, min($this->bufferSize(), strlen($imageData)));

        $this->ffi->EPD_2IN7_Display($this->buffer);
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
