<?php

namespace Thermometer;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Thermometer\Display\Screen;
use Thermometer\Views\BackdropView;
use Thermometer\Views\PortraitView;

class Application
{

    protected Screen $screen;

    protected LoopInterface $loop;

    protected BackdropView $view;

    protected $tickInterval = 60;

    protected $tickTimer;

    public function __construct()
    {
        
    }

    public function initialize()
    {
        $this->screen = new Screen();

        $this->loop = Factory::create();

        $this->view = $this->getInitialView();

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

    public function getInitialView()
    {
        return new PortraitView($this->screen->getWidth(), $this->screen->getHeight());
    }

    public function getScreen()
    {
        return $this->screen;
    }

    public function tick()
    {
        $this->screen->draw($this->view->render());
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
}
