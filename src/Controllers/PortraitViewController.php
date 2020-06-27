<?php

namespace Thermometer\Controllers;

use Thermometer\Responses\RedirectResponse;
use Thermometer\Services\Measurements;
use Thermometer\Views\PortraitView;

class PortraitViewController implements Controller
{

    protected PortraitView $view;

    protected $sensors = [];
    protected Measurements $measurements;

    protected $units = [
        'celsius' => '° C',
    ];

    public function __construct(PortraitView $view, Measurements $measurements)
    {
        $this->view = $view;
        $this->sensors = require __DIR__ . '/../../config/sensors.php';
        $this->measurements = $measurements;
    }

    protected function queryMeasurements()
    {
        return $this->measurements->latest($this->sensors);
    }

    public function tick()
    {
        $measurements = $this->queryMeasurements();

        $sections = array_map(function ($sensor) use ($measurements) {
            return [
                'value' => round($measurements[$sensor['metric']], 2),
                'name' => $sensor['name'],
                'unit' => $this->units[$sensor['unit']],
            ];
        }, $this->sensors);

        $viewData = [
            'date' => date('d.m.Y H:i'),
            'sections' => $sections,
        ];

        return $this->view->with($viewData);
    }

    public function onPrimaryPressed()
    {
        return new RedirectResponse(SystemInfoController::class);
    }

    public function onSecondaryPressed()
    {
        return $this->tick();
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
