<?php
/**
 * 用豆制品调味。
 */
namespace DecoratorPattern\StarBuzz\Decorator;

use DecoratorPattern\StarBuzz\Component\Beverage;

class Soy extends CondimentDecorator
{
    private $beverage;

    // 由构造器将实例记录在实例变量中。
    public function __construct(Beverage $beverage)
    {
        $this->beverage = $beverage;
    }

    public function getDescription()
    {
        return $this->beverage->getDescription() . ',Soy';
    }

    public function cost()
    {
        return 0.10 + $this->beverage->cost();
    }
}