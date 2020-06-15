<?php

use Dotenv\Dotenv;
use Thermometer\Application;

require_once "vendor/autoload.php";

$env = Dotenv::createImmutable(__DIR__);
$env->load();

$app = new Application;

$app->initialize();
$app->run();
