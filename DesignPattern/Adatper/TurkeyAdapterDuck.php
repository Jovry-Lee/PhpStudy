<?php

/**
 * 适配器模式示例。
 *
 * 果有一个动物走起路来像只鸭子,叫起来像只鸭子,那么他可能是一只包装了鸭子适配器的火鸡
 */

/**
 * 鸭子接口,具备呱呱叫和飞行的能力.
 */
interface Duck
{
    // 鸭子叫.
    public function quack();
    // 飞行能力.
    public function fly();
}

/**
 * 火鸡接口.
 */
interface Turkey
{
    // 火鸡咯咯叫.
    public function gobble();
    // 火鸡会飞,但飞不远.
    public function fly();
}

/**
 * 绿头鸭是鸭子的实现.
 */
class MallardDuck implements Duck
{
    public function quack()
    {
        echo "Quack\n";
    }

    public function fly()
    {
        echo "I'm flying\n";
    }
}

/**
 * 野火鸡是火鸡的实现.
 */
class WildTurkey implements Turkey
{

    public function gobble()
    {
        echo "Gobble gobble\n";
    }

    public function fly()
    {
        echo "I'm flying a short distance\n";
    }
}

/**
 * 鸭子适配器(让火鸡来充当鸭子)
 *
 * 首先需要实现想要转换成的类型接口,也就是客户所期望看到的接口.
 */
class TurkeyAdapter implements Duck
{
    /**@var Turkey $turkey*/
    private $turkey;

    // 接着需要取得适配的对象引用.
    public function __construct(Turkey $turkey)
    {
        $this->turkey = $turkey;
    }

    // 实现接口中的所有方法,试下转换.
    public function quack()
    {
        $this->turkey->gobble();
    }

    // 虽然两个接口都具备了fly方法,火鸡的飞行距离短,不像鸭子可以长途飞行,要让鸭子的飞行和火鸡的飞行对应,必须连续调用火鸡的fly来完成.
    public function fly()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->turkey->fly();
        }
    }

}

// 测试.
// 创建一只鸭子.
$duck = new MallardDuck();
//  创建一只火鸡.
$turkey = new WildTurkey();

// 将火鸡包装金一个火鸡适配器,使它看起来象一只鸭子.
$turkeyAdapter = new TurkeyAdapter($turkey);
echo "The Turkey says...\n";
$turkey->gobble();
$turkey->fly();

echo "\nThe Duck says...\n";
$duck->quack();
$duck->fly();

echo "\nThe TurkeyAdapter says...\n";
$turkeyAdapter->quack();
$turkeyAdapter->fly();

