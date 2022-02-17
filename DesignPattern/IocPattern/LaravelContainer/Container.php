<?php

namespace IocPattern\LaravelContainer;

use Closure;
use ReflectionClass;
use ReflectionParameter;

/**
 * 核心简化容器实现。
 */
class Container
{
    // 用于提供实例的回调函数。
    protected $bindings = [];

    // 绑定接口和生成相应实例的回调函数。(实现lazy加载，容器只有在解析时闭包才会真正进行运行)
    public function bind($abstract, $concrete = null, $shared = false)
    {
        // 绑定的参数非回调函数，则默认生成回调函数。
         if (!$concrete instanceof Closure) {
             $concrete = $this->getClosure($abstract, $concrete);
         }

         $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    // 默认生成实例的回调函数。
    protected function getClosure($abstract, $concrete)
    {
        return function (Container $container) use ($abstract, $concrete) {
            // 自我绑定的情况。
            if ($abstract == $concrete) {
                return $container->build($concrete);
            }

            // 解析对象。
            return $container->make($concrete);
        };
    }

    // 生成实例对象, 解决接口和要实例对象的依赖关系。
    public function make($abstract)
    {
        $concrete = $this->getConcrete($abstract);
        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($abstract);
        }

        return $object;
    }

    // 是否可实例化。
    protected function isBuildable($concrete, $abstract)
    {
        return $abstract == $concrete || $concrete instanceof Closure;
    }

    // 获取绑定的回调函数
    protected function getConcrete($abstract)
    {
        if (!isset($this->bindings[$abstract])) {
            return $abstract;
        }
        return $this->bindings[$abstract]['concrete'];
    }

    // 实例化对象，通过反射机制实现类的实例化。
    public function build($concrete)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }

        $reflector = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            echo "Target [$concrete] is not instantiable.";
        }

        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $instances = $this->getDependencies($dependencies);
        return $reflector->newInstanceArgs($instances);
    }

    // 解决通过反射实例化对象时的依赖。
    protected function getDependencies($parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if (is_null($dependency)) {
                $dependencies[] = null;
            } else {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }
        return (array)$dependencies;
    }

    protected function resolveClass(ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
    }
}