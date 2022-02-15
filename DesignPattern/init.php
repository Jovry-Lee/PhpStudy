<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '../Autoloader.php';

// 自动加载.
Autoloader::getInstance()
    ->addRoot(__DIR__ . DIRECTORY_SEPARATOR)
    ->init();

