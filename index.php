<?php

use React\EventLoop\Factory;
use Thermometer\Display\Button;
use Thermometer\Display\Screen;

require_once "vendor/autoload.php";

$width = 176;
$height = 264;

function createImage($width, $height)
{
    $im = new Imagick();
    $im->setColorSpace(Imagick::COLORSPACE_GRAY);
    $im->newImage($width, $height, new ImagickPixel('white'));
    $im->setImageFormat('GRAY');

    $text = date("d.m.Y H:i:s");

    $draw = new ImagickDraw();
    //$draw->setFont('Arial');
    $draw->setFontSize(14);
    $draw->setFillColor('black');

    $im->annotateImage($draw, 10, 20, 0, $text);

    $im->posterizeImage(2, true);
    $im->setImageDepth(1);
    return $im->getImageBlob();
}

$screen = new Screen($width, $height);

$button = new Button();
$button->register(function () use ($screen) {
    echo "Button pressed!";
    $screen->draw(createImage($screen->getWidth(), $screen->getHeight()));
}, Button::KEY1, Button::EDGE_RISING);

$screen->clear();

$loop = Factory::create();

$loop->addPeriodicTimer(60, function () use ($screen) {
    $screen->draw(createImage($screen->getWidth(), $screen->getHeight()));
});

$loop->addSignal(SIGINT, function () use ($screen, $loop) {
    echo "Set display into sleep mode" . PHP_EOL;
    $screen->sleep();
    echo "Bye!" . PHP_EOL;
    $loop->stop();
});

$screen->draw(createImage($screen->getWidth(), $screen->getHeight()));

$loop->run();