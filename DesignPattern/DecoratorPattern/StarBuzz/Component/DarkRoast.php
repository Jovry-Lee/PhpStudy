<?php

namespace DecoratorPattern\StarBuzz\Component;

class DarkRoast extends Beverage
{
    public function __construct()
    {
        $this->description = '0.99';
    }

    public function cost()
    {
        return 1.89;
    }
}