<?php

namespace Thermometer\Display;

use FFI;

class Button
{
    protected $ffi;
    protected $libraryPath = './epaper/src/gpio.h';

    public const KEY1 = 5;
    public const KEY2 = 6;
    public const KEY3 = 13; // Not working
    public const KEY4 = 19; // Not working

    public const EDGE_FALLING = 1;
    public const EDGE_RISING = 2;
    public const EDGE_BOTH = 3;

    public function __construct()
    {
        $this->ffi = FFI::load($this->libraryPath);
    }

    /**
     * This is just a proof of concept, that attaching of events to button presses is possible.
     * Sadly, the documentation says, that passing closures can leak memory...
     * I hope, this is only, because the reference stays active even after a request is finished in a preload scenario.
     */
    public function register($handler, $button = self::KEY1, $edge = self::EDGE_FALLING)
    {
        $this->ffi->wiringPiISR($button, $edge, $handler);
    }
}
