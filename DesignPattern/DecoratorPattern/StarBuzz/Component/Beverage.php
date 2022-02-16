<?php
/**
 * 饮料抽象类。
 */
namespace DecoratorPattern\StarBuzz\Component;

abstract class Beverage
{
    public $description = 'Unknown Beverage';

    public function getDescription()
    {
        return $this->description;
    }

    public abstract function cost();
}