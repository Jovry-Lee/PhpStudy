<?php

namespace Tests\Arithmetic\LeetCode;

trait Util
{
    public static function getInstance()
    {
        $testName = static::class;
        $className = substr($testName, strpos($testName, '\\'), -4);

        return new $className();
    }
}