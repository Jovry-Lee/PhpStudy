<?php

class singleton {
    // 私有静态成员变量
    private static $_instance = null;

    // 私有构造函数，只在第一次实例化时执行
    private function __construct() {}

    // 私有克隆函数
    private function __clone() {}

    // 获取实例函数
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

$a = singleton::getInstance();
$b = singleton::getInstance();
var_dump($a === $b);


