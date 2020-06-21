<?php

namespace Thermometer\Controllers;

use InfluxDB\Client;
use Thermometer\Views\PortraitView;

class PortraitViewController implements Controller
{

    protected PortraitView $view;

    protected $sensors = [];
    protected Client $client;

    protected $units = [
        'celsius' => '° C',
    ];

    public function __construct(PortraitView $view)
    {
        $this->view = $view;
        $this->sensors = require __DIR__ . '/../../config/sensors.php';
        $this->client = new Client(
            $_ENV['INFLUXDB_HOST'],
            $_ENV['INFLUXDB_PORT'],
            $_ENV['INFLUXDB_USER'],
            $_ENV['INFLUXDB_PASSWORD'],
            $_ENV['INFLUXDB_SSL'],
            $_ENV['INFLUXDB_VERIFY']
        );
    }

    protected function queryMeasurements()
    {
        $database = $this->client->selectDB($_ENV['INFLUXDB_DB']);

        $result = $database->query('SELECT last(value) FROM ' . implode(', ', array_map(fn ($sensor) => $sensor['metric'], $this->sensors)));

        $measurements = [];
        foreach ($this->sensors as $sensor) {
            $measurements[$sensor['metric']] = $result->getPoints($sensor['metric'])[0]['last'];
        }

        return $measurements;
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

        return $this->view->with($viewData)->render();
    }

    public function onPrimaryPressed()
    {
        echo "Primary Button pressed" . PHP_EOL;
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
