<?php

require_once "MiddlewareSimulation.php";

function getSlice()
{
    return function ($stack, $pipe) {
        return function () use ($stack, $pipe) {
            return $pipe::handle($stack);
        };
    };
}

function then()
{
    $pipes = [
        CheckForMaintenanceMode::class,
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class
    ];
    $firstSlice = function () {
        echo "请求向路由器传递，返回响应\n";
    };
    // 相当于是栈，先进后出，因此需进行一次反转，保证执行顺序。
    $pipes = array_reverse($pipes);
    call_user_func(
        array_reduce($pipes, getSlice(), $firstSlice)
    );
}

then();
