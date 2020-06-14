<?php

use React\EventLoop\Factory;
use Thermometer\Display\Button;
use Thermometer\Display\Screen;
use Thermometer\Views\PortraitView;

require_once "vendor/autoload.php";

$width = 176;
$height = 264;

$screen = new Screen($width, $height);

$view = new PortraitView($width, $height);

$button = new Button();
$button->register(function () use ($screen, $view) {
    echo "Button pressed!";
    $screen->draw($view->render());
}, Button::KEY1, Button::EDGE_RISING);

$screen->clear();

$loop = Factory::create();

$loop->addPeriodicTimer(60, function () use ($screen, $view) {
    $screen->draw($view->render());
});

$loop->addSignal(SIGINT, function () use ($screen, $loop) {
    echo "Put display into sleep mode" . PHP_EOL;
    $screen->sleep();
    echo "Bye!" . PHP_EOL;
    $loop->stop();
});

$screen->draw($view->render());

$loop->run();