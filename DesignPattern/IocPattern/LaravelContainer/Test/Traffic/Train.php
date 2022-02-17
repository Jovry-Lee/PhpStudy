<?php

namespace IocPattern\LaravelContainer\Test\Traffic;

// 火车。
class Train implements Visit
{
    public function go()
    {
        echo "Going to Tibet by train!!!";
    }
}
