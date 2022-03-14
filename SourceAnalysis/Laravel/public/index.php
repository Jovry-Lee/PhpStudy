<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists(__DIR__.'/../storage/framework/maintenance.php')) {
    require __DIR__.'/../storage/framework/maintenance.php';
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

// composer的自动加载(实现了PSR0、PSR4标准)。（Laravel框架的初始化需要composer自动加载协助，因此在入口文件中利用composer实现自动加载功能）
require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

// 获取Laravel核心的IoC容器。
// 实现服务容器实例化和基本注册，包括服务容器本身注册、基础服务提供者注册、核心类别名注册和基本路径注册。
$app = require_once __DIR__.'/../bootstrap/app.php';

// 制造Http请求的内核。
$kernel = $app->make(Kernel::class);

// Laravel所有功能服务的注册加载。
$response = tap($kernel->handle( // Laravel通过全局$_SERVER数组构造一个Http请求.
    $request = Request::capture() // 通过服务器提供的变量创建一个HTTP请求实例。
))->send();

$kernel->terminate($request, $response);
