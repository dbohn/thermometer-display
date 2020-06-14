<?php

namespace Thermometer\Views;

use Imagick;
use ImagickDraw;

abstract class BackdropView
{

    protected $width;
    protected $height;

    protected $image;

    protected $viewData = [];

    public abstract function viewFile(): string;

    public abstract function annotate(): void;

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function with(array $data)
    {
        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

    public function render()
    {
        $this->image = new Imagick();
        $this->image->setColorSpace(Imagick::COLORSPACE_GRAY);

        $this->image->setSize($this->width, $this->height);
        $this->image->setFormat('GRAY');
        $this->image->readImage($this->viewFile());

        $this->annotate();

        $this->image->posterizeImage(2, false);
        $this->image->setImageDepth(1);
        return $this->image->getImageBlob();
    }

    public function addString($text, $xPos, $yPos, $fontSize = 14, $angle = 0): void
    {
        $draw = new ImagickDraw();
        //$draw->setFont('Arial');
        $draw->setFontSize($fontSize);
        $draw->setFillColor('black');

        $this->image->annotateImage($draw, $xPos, $yPos, $angle, $text);
    }

    public function __get($key)
    {
        return $this->viewData[$key] ?? null;
    }
}
