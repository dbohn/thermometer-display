<?php

namespace Thermometer\Controllers;

use Thermometer\Responses\RedirectResponse;
use Thermometer\Views\PortraitView;

class SystemInfoController implements Controller
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
            'sections' => [
                [
                    'value' => 'INFO',
                    'name' => 'INFO',
                    'unit' => '',
                ]
            ],
        ];

        return $this->view->with($viewData)->render();
    }

    public function onPrimaryPressed()
    {
        return new RedirectResponse(PortraitViewController::class);
    }

    public function onSecondaryPressed()
    {
        echo "Secondary Button pressed" . PHP_EOL;
    }

    public function onPreviousPressed()
    {
        echo "Previous Button pressed" . PHP_EOL;
    }

    public function onNextPressed()
    {
        echo "Next Button pressed" . PHP_EOL;
    }
}
