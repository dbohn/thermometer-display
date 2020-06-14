<?php

use Thermometer\Application;

require_once "vendor/autoload.php";

$app = new Application;

$app->initialize();
$app->run();

/*
$button = new Button();
$button->register(function () use ($screen, $view) {
    echo "Button pressed!";
    $screen->draw($view->render());
}, Button::KEY1, Button::EDGE_RISING);
*/