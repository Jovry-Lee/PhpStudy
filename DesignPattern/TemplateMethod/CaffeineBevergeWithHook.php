<?php

// 封装算法:模板方法模式.

// 咖啡和茶的抽象共同点:咖啡因饮料.
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

class TeaWithHook extends CaffeineBeverageWithHook
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

class CoffeeWithHook extends CaffeineBeverageWithHook
{
    public function brew()
    {
        echo "Dripping Coffee through filter!\n";
    }

    public function addCondiments()
    {
        echo "Adding Sugar and Milk!!\n";
    }

    public function customerWantsConditions()
    {
        $answer = $this->getUserInput();
        if (strtolower(substr($answer, 0, 1)) == 'y') {
            return true;
        } else {
            return false;
        }
    }

    public function getUserInput()
    {
        fwrite(STDOUT, "Would you like milk and sugar woth your coffee (y/n)");
        try {
            $answer = trim(fgets(STDIN));
        } catch (\Exception $e){
            throw $e;
        }

        if (empty($answer)) {
            $answer = 'no';
        }

        return $answer;
    }
}

// 创建一杯茶.
$teaHook = new TeaWithHook();
// 创建已被咖啡.
$coffeeHook = new CoffeeWithHook();

echo "\nMaking tea...\n";
$teaHook->prepareRecipe();

echo "\nMaking coffee...\n";
$coffeeHook->prepareRecipe();