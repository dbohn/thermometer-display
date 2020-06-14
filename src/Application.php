<?php

namespace Thermometer;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Thermometer\Controllers\Controller;
use Thermometer\Controllers\PortraitViewController;
use Thermometer\Display\Buttons;
use Thermometer\Display\Screen;

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

        $this->buttons = new Buttons($this->loop);

        $this->controller = $this->initializeController($this->getDefaultController());

        $this->registerShutdownHandler();
        $this->registerTick();
        $this->registerButtonCallbacks();

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

    protected function registerButtonCallbacks()
    {
        // Variante 1: Eine Methode buttonPressed im Controller, die Taste als Argument bekommt
        $this->buttons->register(fn () => $this->callController('buttonPressed', Buttons::KEY1), Buttons::KEY1, Buttons::EDGE_RISING);
        $this->buttons->register(fn () => $this->callController('buttonPressed', Buttons::KEY2), Buttons::KEY2, Buttons::EDGE_RISING);
        $this->buttons->register(fn () => $this->callController('buttonPressed', Buttons::KEY3), Buttons::KEY3, Buttons::EDGE_RISING);
        $this->buttons->register(fn () => $this->callController('buttonPressed', Buttons::KEY4), Buttons::KEY4, Buttons::EDGE_RISING);

        // Variante 2: Für jede Taste eine mögliche Callback-Funktion.
        // callController ignoriert nicht existierende Handler!
        /*$this->buttons->register(function () {
            $this->callController('button1Pressed');
        }, Buttons::KEY1, Buttons::EDGE_RISING);

        $this->buttons->register(function () {
            $this->callController('button2Pressed');
        }, Buttons::KEY2, Buttons::EDGE_RISING);*/
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
    }
}
