<?php

require_once __DIR__ . "/../../init.php";

use TemplateMethodPattern\CaffeineBeverageWithHook\TeaWithHook;
use TemplateMethodPattern\CaffeineBeverageWithHook\CoffeeWithHook;

// 创建一杯茶.
$teaHook = new TeaWithHook();
// 创建已被咖啡.
$coffeeHook = new CoffeeWithHook();

echo "\nMaking tea...\n";
$teaHook->prepareRecipe();

echo "\nMaking coffee...\n";
$coffeeHook->prepareRecipe();
