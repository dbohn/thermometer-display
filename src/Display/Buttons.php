<?php

namespace Thermometer\Display;

use FFI;

class Buttons
{
    protected $ffi;
    protected $libraryPath = './epaper/src/gpio.h';

    public const PRIMARY = 5;
    public const PREVIOUS = 6;
    public const NEXT = 13;
    public const SECONDARY = 19;

    public const EDGE_FALLING = 1;
    public const EDGE_RISING = 2;
    public const EDGE_BOTH = 3;

    protected const MODE_INPUT = 0;
    protected const MODE_OUTPUT = 1;

    protected const PUD_OFF = 0;
    protected const PUD_DOWN = 1;
    protected const PUD_UP = 2;

    protected const DEBOUNCE_INTERVAL = 0.3;

    protected $locked = [
        self::PRIMARY => false,
        self::PREVIOUS => false,
        self::NEXT => false,
        self::SECONDARY => false,
    ];

    public function __construct()
    {
        $this->ffi = FFI::load($this->libraryPath);
    }

    /**
     * Register an ISR, that is called, as the provided button is pressed.
     * This uses the possibility to bind PHP closures to function references in the FFI.
     * Sadly, the documentation says, that this feature could leak memory...
     * I hope, that this does only affect preload scenarios, where the closures are not taken down.
     */
    public function register($handler, $button = self::PRIMARY, $edge = self::EDGE_FALLING)
    {
        $this->ffi->pinMode($button, self::MODE_INPUT);
        $this->ffi->pullUpDnControl($button, self::PUD_UP);
        $this->ffi->wiringPiISR($button, $edge, fn () => $this->debounce($button, $handler));
    }

    /**
     * Due to the mechanics of the buttons, the ISR might get called multiple times.
     * This introduces a 300ms (set by DEBOUNCE_INTERVAL) cooldown period, in which no new button presses are recognized.
     */
    protected function debounce($button, $handler)
    {
        $now = microtime(true);
        if ($this->locked[$button] !== false && $now < ($this->locked[$button] + self::DEBOUNCE_INTERVAL) && $now >= $this->locked[$button] ) {
            return;
        }

        $this->locked[$button] = $now;

        $handler();
    }
}
