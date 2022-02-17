<?php

use ObserverPattern\WeatherStation\CurrentConditionsDisplay;
use ObserverPattern\WeatherStation\WeatherData;

require_once __DIR__ . "/../../init.php";

// 创建一个WeatherData对象。
$weatherStation = new WeatherData();
$currentDisplay = new CurrentConditionsDisplay($weatherStation);

$weatherStation->setMeasurements(80,65,30.4);
$weatherStation->setMeasurements(82,70,29.2);
$weatherStation->setMeasurements(78,90,29.2);