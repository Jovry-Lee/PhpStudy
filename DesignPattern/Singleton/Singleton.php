<?php

class singleton {
    // 私有静态成员变量
    private static $instance = null;

    // 私有构造函数，只在第一次实例化时执行
    private function __construct() {}

    // 获取实例函数
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}

$a = singleton::getInstance();
$b = singleton::getInstance();
var_dump($a === $b);


