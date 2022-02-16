<?php

namespace DecoratorPattern\StarBuzz\Component;

class Espresso extends Beverage
{
    public function __construct()
    {
        $this->description = 'espresso';
    }

    public function cost()
    {
        return 1.99;
    }
}