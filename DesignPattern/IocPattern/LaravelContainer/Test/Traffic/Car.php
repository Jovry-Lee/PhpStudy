<?php

namespace IocPattern\LaravelContainer\Test\Traffic;

// 开车。
class Car implements Visit
{
    public function go()
    {
        echo "Driving car to Tibet!!!";
    }
}