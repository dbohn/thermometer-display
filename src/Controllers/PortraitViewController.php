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
            'date' => 'Datum: ' . date('d.m.Y H:i'),
            'sections' => [
                [
                    'value' => '23.40',
                    'name' => 'Kalt',
                    'unit' => '°C',
                ],
                [
                    'value' => '27.03',
                    'name' => 'Temperiert',
                    'unit' => '°C'
                ],
            ],
        ];

        return $this->view->with($viewData)->render();
    }

    public function buttonPressed($key)
    {
        echo "Button $key pressed!";
    }
}
