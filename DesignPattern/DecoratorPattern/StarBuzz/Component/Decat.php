<?php

namespace DecoratorPattern\StarBuzz\Component;

class Decat extends Beverage
{
    public function __construct()
    {
        $this->description = 'Decat';
    }

    public function cost()
    {
        return 1.05;
    }
}