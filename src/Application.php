<?php

namespace Thermometer;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Thermometer\Controllers\Controller;
use Thermometer\Controllers\PortraitViewController;
use Thermometer\Display\Screen;

class Application
{

    use ResolvesControllers;

    protected Screen $screen;

    protected LoopInterface $loop;

    protected Controller $controller;

    protected $tickInterval = 60;

    protected $tickTimer;

    public function __construct()
    {
        
    }

    public function initialize()
    {
        $this->screen = new Screen();

        $this->loop = Factory::create();

        $this->controller = $this->initializeController($this->getDefaultController());

        $this->registerShutdownHandler();
        $this->registerTick();

        $this->screen->clear();
    }

    public function run()
    {
        // We execute the first tick manually to have some initial output
        $this->tick();

        $this->loop->run();
    }

    public function getDefaultController()
    {
        return PortraitViewController::class;
    }

    public function getScreen()
    {
        return $this->screen;
    }

    public function tick()
    {
        $this->callController('tick');
    }

    protected function registerShutdownHandler()
    {
        $this->loop->addSignal(SIGINT, function () {
            echo "Put display into sleep mode" . PHP_EOL;
            $this->screen->sleep();
            echo "Bye!" . PHP_EOL;
            $this->loop->stop();
        });
    }

    protected function registerTick()
    {
        $this->tickTimer = $this->loop->addPeriodicTimer($this->tickInterval, function () {
            $this->tick();
        });
    }

    protected function callController($method, ...$args)
    {
        $result = $this->controller->$method(...$args);

        if (is_string($result)) {
            $this->screen->draw($result);
        }
    }
}
