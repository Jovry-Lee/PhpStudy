<?php

require_once __DIR__ . "/../../../Autoloader.php";
\Bootstrap\Autoloader::instance()->addRoot(__DIR__ . "/../")->init();

// 创建一个WeatherData对象。
$weatherStation = new \WeatherStation\WeatherData();
$currentDisplay = new \WeatherStation\CurrentConditionsDisplay($weatherStation);

$weatherStation->setMeasurements(80,65,30.4);
$weatherStation->setMeasurements(82,70,29.2);
$weatherStation->setMeasurements(78,90,29.2);