<?php

namespace TemplateMethodPattern\CaffeineBeverageWithHook;

abstract class CaffeineBeverageWithHook
{
    // 用同一个prepareRecipe()方法来处理茶和咖啡,prepareRecipe()被申明为final.不让子类覆盖此方法.
    final function prepareRecipe()
    {
        $this->boilWater();
        $this->brew();
        $this->pourInCup();
        // 加入一个小小的条件语句,条件是由customerWantsConditions()决定的.
        if ($this->customerWantsConditions()) {
            $this->addCondiments();
        }
    }

    // 咖啡和查处理这些方法的做法不同,所以者来嗯个方法必须被声明为抽象.
    abstract function brew();
    abstract function addCondiments();

    function boilWater()
    {
        echo "Boiling water!\n";
    }

    function pourInCup()
    {
        echo "Pouring into cup!\n";
    }

    // 这是一个钩子,子类可以覆盖此方法.
    function customerWantsConditions()
    {
        return true;
    }
}