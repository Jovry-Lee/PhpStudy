<?php

namespace IocPattern\LaravelContainer\Test\Traffic;

// 步行。
class Leg implements Visit
{
    public function go()
    {
        echo "Walking to Tibet!!!";
    }
}