<?php
namespace Arithmetic;

class Util
{
    // 判断一个数是否是质数。
    public static function isPrime($num)
    {
        for ($i = 2; $i * $i <= $num; $i++) {
            if ($num % $i == 0) {
                return false;
            }
        }
        return true;
    }

}