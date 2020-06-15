<?php

namespace Thermometer\Display;

use FFI;
use React\EventLoop\LoopInterface;

class Buttons
{
    protected $ffi;
    protected $libraryPath = './epaper/src/gpio.h';

    protected LoopInterface $loop;

    public const KEY1 = 5;
    public const KEY2 = 6;
    public const KEY3 = 13; // Not working
    public const KEY4 = 19; // Not working

    public const EDGE_FALLING = 1;
    public const EDGE_RISING = 2;
    public const EDGE_BOTH = 3;

    protected $locked = [
        self::KEY1 => false,
        self::KEY2 => false,
        self::KEY3 => false,
        self::KEY4 => false,
    ];

    public function __construct(LoopInterface $loop)
    {
        $this->ffi = FFI::load($this->libraryPath);
        $this->loop = $loop;
    }

    /**
     * This is just a proof of concept, that attaching of events to button presses is possible.
     * Sadly, the documentation says, that passing closures can leak memory...
     * I hope, this is only, because the reference stays active even after a request is finished in a preload scenario.
     */
    public function register($handler, $button = self::KEY1, $edge = self::EDGE_FALLING)
    {
        $this->ffi->wiringPiISR($button, $edge, fn () => $this->debounce($button, $handler));
    }

    protected function debounce($button, $handler)
    {
        if ($this->locked[$button]) {
            return;
        }

        $this->locked[$button] = true;
        $this->loop->addTimer(0.1, function () use ($button) {
            echo "Unlock";
            $this->locked[$button] = false;
        });

        $handler();
    }
}
