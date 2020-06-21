<?php

namespace Thermometer;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Thermometer\Controllers\Controller;
use Thermometer\Controllers\PortraitViewController;
use Thermometer\Display\Buttons;
use Thermometer\Display\Screen;
use Thermometer\Responses\RedirectResponse;
use Thermometer\Views\View;

class Application
{

    use ResolvesControllers;

    protected Screen $screen;

    protected Buttons $buttons;

    protected LoopInterface $loop;

    protected Controller $controller;

    protected $tickInterval = 60;

    protected $tickTimer;

    public function __construct()
    {
        
    }

    public function initialize()
    {
        $this->loop = Factory::create();

        $this->screen = new Screen();

        $this->buttons = new Buttons();

        //$this->controller = $this->initializeController($this->getDefaultController());

        $this->registerShutdownHandler();
        $this->registerTick();
        $this->registerButtonCallbacks();

        $this->screen->clear();
    }

    public function run()
    {
        $this->loadController($this->getDefaultController());

        $this->loop->run();
    }

    protected function loadController($controller)
    {
        $this->controller = $this->initializeController($controller);

        $this->tick();
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

    protected function registerButtonCallbacks()
    {
        $this->buttons->register(fn () => $this->callController('onPrimaryPressed'), Buttons::PRIMARY, Buttons::EDGE_RISING);
        $this->buttons->register(fn () => $this->callController('onPreviousPressed'), Buttons::PREVIOUS, Buttons::EDGE_RISING);
        $this->buttons->register(fn () => $this->callController('onNextPressed'), Buttons::NEXT, Buttons::EDGE_RISING);
        $this->buttons->register(fn () => $this->callController('onSecondaryPressed'), Buttons::SECONDARY, Buttons::EDGE_RISING);
    }

    protected function registerTick()
    {
        $this->tickTimer = $this->loop->addPeriodicTimer($this->tickInterval, function () {
            $this->tick();
        });
    }

    protected function callController($method, ...$args)
    {
        if (!method_exists($this->controller, $method)) {
            return;
        }

        $result = $this->controller->$method(...$args);

        if (is_string($result)) {
            $this->screen->draw($result);
        }

        if ($result instanceof View) {
            $this->screen->draw($result->render());
        }

        if ($result instanceof RedirectResponse) {
            $this->loadController($result->getDestination());
        }
    }
}
