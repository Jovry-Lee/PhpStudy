<?php

class Mocha extends CondimentDecorator
{
    // 让Mocha能够引用一个Beverage，用示例变量记录饮料，也就是被装饰者。
    private $beverage;
    // 由构造器将实例记录在实例变量中。
    public function __construct(Beverage $beverage)
    {
        $this->beverage = $beverage;
    }

    public function getDescription()
    {
        return $this->beverage->getDescription() . ',Mocha';
    }

    public function cost()
    {
        return 0.20 + $this->beverage->cost();
    }
}