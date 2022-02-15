<?php

$format = 'C3';

$string = pack($format, 67, 68, -1);
var_dump($string);

$order = array();
for ($i = 0; $i < strlen($string); $i++) {
    $order[] = ord($string[$i]);
}

var_export($order);