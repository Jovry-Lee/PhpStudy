<?php

$format = 'N';

$string = pack($format, 123456789);
var_dump($string);

$order = array();
for ($i = 0; $i < strlen($string); $i++) {
    $order[] = ord($string[$i]);
}

var_export($order);