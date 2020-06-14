<?php

namespace Thermometer\Controllers;

use Thermometer\Views\PortraitView;

class PortraitViewController implements Controller
{

    protected PortraitView $view;

    public function __construct(PortraitView $view)
    {
        $this->view = $view;
    }

    public function tick()
    {
        $viewData = [
            'date' => date('d.m.Y H:i'),
        ];

        return $this->view->render($viewData);
    }
}
