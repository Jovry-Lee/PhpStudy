<?php

$string = pack('h3', 281);
var_dump($string);

$order = array();
for ($i = 0; $i < strlen($string); $i++) {
    $order[] = ord($string[$i]);
}

var_export($order);