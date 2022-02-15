<?php
// 简单工厂模式.

/**
 * 简单披萨工厂。
 */
class SimplePizzaFactory
{
    /**
     * 使用该方法来实例化对象。
     */
    public function createPizza(string $type)
    {
        switch ($type) {
            case 'cheese':
                $pizza = new CheesePizza();
                break;
            case 'pepperoni':
                $pizza = new PepperoniPizza();
                break;
            case 'clam':
                $pizza = new ClamPizza();
                break;
            case 'veggie':
                $pizza = new VeggiePizza();
                break;
            default:
                $pizza = null;
        }
        return $pizza;
    }
}

class PizzaStore
{
    /** @param SimplePizzaFactory*/
    private $factory;
    public function __construct(SimplePizzaFactory $factory)
    {
        $this->factory = $factory;
    }

    public function orderPizza(string $type)
    {
        $pizza = $this->factory->createPizza($type);

        $pizza->prepare();
        $pizza->bake();
        $pizza->cut();
        $pizza->box();
        return $pizza;
    }
}



