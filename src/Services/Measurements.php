<?php

namespace Thermometer\Services;

use InfluxDB\Database;

class Measurements
{
    protected $db;

    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    public function latest($sensors)
    {
        $result = $this->db->query('SELECT last(value) FROM ' . implode(', ', array_map(fn ($sensor) => $sensor['metric'], $sensors)));

        $measurements = [];
        foreach ($sensors as $sensor) {
            $measurements[$sensor['metric']] = $result->getPoints($sensor['metric'])[0]['last'];
        }

        return $measurements;
    }
}
