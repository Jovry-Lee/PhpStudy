<?php

interface Middleware
{
    public static function handle(Closure $next);
}

class VerifyCsrfToken implements Middleware
{
    public static function handle(Closure $next)
    {
        echo "验证Csrf-Token\n";
        $next();
    }
}

class ShareErrorsFromSession implements Middleware
{
    public static function handle(Closure $next)
    {
        echo "若session中有'errors'变量，则共享它\n";
        $next();
    }
}

class StartSession implements Middleware
{
    public static function handle(Closure $next)
    {
        echo "开启session，获取数据\n";
        $next();
        echo "保存数据，关闭session\n";
    }
}

class AddQueuedCookiesToResponse implements Middleware
{
    public static function handle(Closure $next)
    {
        $next();
        echo "添加下一次请求需要的cookie\n";
    }
}

class EncryptCookies implements Middleware
{
    public static function handle(Closure $next)
    {
        echo "对输入请求的cookie进行解密\n";
        $next();
        echo "对输出响应的cookie进行加密\n";
    }
}

class CheckForMaintenanceMode implements Middleware
{
    public static function handle(Closure $next)
    {
        echo "确定当前程序是否处于维护状态\n";
        $next();
    }
}