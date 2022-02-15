<?php

class Singleton
{
    private static $instance;
    private function __construct(){}

public static function  getInstance()
{
    if (static::$instance == null) {
        static::$instance = new self();
    }
    return static::$instance;
}
}

$singleton = Singleton::getInstance();