<?php

/**
 * 抽象类,它被声明为抽象,用作基类,子类必须是县其操作.
 */
abstract class AbstractClass
{
    // 模板方法定义了一连串的步骤,每个步骤由一个方法代表.
    final function templateMethod()
    {
        $this->primitiveOperation1();
        $this->primitiveOperation2();
        $this->concreteOperation();
    }

    // 以下两个抽象方法,其子类必须实现它们.
    abstract function primitiveOperation1();
    abstract function primitiveOperation2();

    final function concreteOperation()
    {
        // 这里是实现.
    }

    // 可以有"默认不做事的方法",我们称这种方法为"hook"(钩子),子类可以视情况决定要不要覆盖它们.
    function hook(){}
}