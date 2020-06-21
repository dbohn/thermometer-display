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
    public const KEY3 = 13;
    public const KEY4 = 19;

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
        $this->ffi->pinMode($button, self::MODE_INPUT);
        $this->ffi->pullUpDnControl($button, self::PUD_UP);
        $this->ffi->wiringPiISR($button, $edge, fn () => $this->debounce($button, $handler));
    }

    protected function debounce($button, $handler)
    {
        $now = microtime(true);
        if ($this->locked[$button] !== false && $now < ($this->locked[$button] + self::DEBOUNCE_INTERVAL) ) {
            return;
        }

        $this->locked[$button] = $now;

        $handler();
    }
}
