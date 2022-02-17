<?php

require_once __DIR__ . "/../../../init.php";

use IocPattern\LaravelContainer\Container;
use IocPattern\LaravelContainer\Test\Traffic\Train;
use IocPattern\LaravelContainer\Test\Traveller;

// 实例化容器。
$app = new Container();
// 绑定
$app->bind('Visit', Train::class);
$app->bind('Traveller', Traveller::class);

// 通过容器实现依赖注入，完成类的实例化。
$traveller = $app->make('Traveller');
$traveller->visitTibet();

