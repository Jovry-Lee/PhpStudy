<?php

use DecoratorPattern\StarBuzz\Component\DarkRoast;
use DecoratorPattern\StarBuzz\Component\Espresso;
use DecoratorPattern\StarBuzz\Component\HouseBlend;
use DecoratorPattern\StarBuzz\Decorator\Mocha;
use DecoratorPattern\StarBuzz\Decorator\Soy;
use DecoratorPattern\StarBuzz\Decorator\Whip;

require_once __DIR__ . DIRECTORY_SEPARATOR . "../../init.php";

$beverage = new Espresso();
echo $beverage->getDescription() . " $" . $beverage->cost() . "\n";

$beverage2 = new DarkRoast();
$beverage2 = new Mocha($beverage2);
$beverage2 = new Mocha($beverage2);
$beverage2 = new Whip($beverage2);
echo $beverage2->getDescription() . " $" . $beverage2->cost() . "\n";

$beverage3 = new HouseBlend();
$beverage3 = new Soy($beverage3);
$beverage3 = new Whip($beverage3);
$beverage3 = new Mocha($beverage3);
echo $beverage3->getDescription() . " $" . $beverage3->cost() . "\n";

