<?php

interface Step
{
    public static function go(Closure $next);
}

class FirstStep implements Step
{
    public static function go(Closure $next)
    {
        echo "开启session，获取数据\n";
        $next();
        echo "保存数据，关闭session\n";
    }
}

/**
 * @param $step 前一次迭代的返回值。
 * @param $className 当前迭代的数组的item。
 */
function goFun($step, $className)
{
    return function () use ($step, $className) {
        return $className::go($step);
    };
}

function then()
{
    $steps = [
        FirstStep::class
    ];

    $prepare = function () {
        echo "请求向路由传递，返回响应\n";
    };
    $go = array_reduce($steps, "goFun", $prepare);
    $go();
}

then();