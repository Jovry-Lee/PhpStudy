<?php

// 封装算法:模板方法模式.

// 咖啡和茶的抽象共同点:咖啡因饮料.
abstract class CaffeineBeverage
{
    // 用同一个prepareRecipe()方法来处理茶和咖啡,prepareRecipe()被申明为final.不让子类覆盖此方法.
    final function prepareRecipe()
    {
        $this->boilWater();
        $this->brew();
        $this->pourInCup();
        $this->addCondiments();
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
}

class Tea extends CaffeineBeverage
{
    public function brew()
    {
        echo "Steepign the tea!\n";
    }

    public function addCondiments()
    {
        echo "Adding Lemon!\n";
    }
}

class Coffee extends CaffeineBeverage
{
    public function brew()
    {
        echo "Dripping Coffee through filter!\n";
    }

    public function addCondiments()
    {
        echo "Adding Sugar and Milk!!\n";
    }
}

$myTea = new Tea();
$myTea->prepareRecipe();