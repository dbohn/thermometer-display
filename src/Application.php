<?php

namespace Thermometer;

use InfluxDB\Client;
use InfluxDB\Database;
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

    public function initialize()
    {
        $this->bind(Client::class, fn () => new Client(
            $_ENV['INFLUXDB_HOST'],
            $_ENV['INFLUXDB_PORT'],
            $_ENV['INFLUXDB_USER'],
            $_ENV['INFLUXDB_PASSWORD'],
            $_ENV['INFLUXDB_SSL'],
            $_ENV['INFLUXDB_VERIFY']
        ));

        $this->bind(Database::class, fn ($app) => $app->make(Client::class)->selectDB($_ENV['INFLUXDB_DB']));

        $this->loop = Factory::create();

        $this->screen = new Screen();

        $this->buttons = new Buttons();

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
        $this->controller = $this->make($controller);

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
