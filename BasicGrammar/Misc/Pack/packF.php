<?php

$format = 'f';

$string = pack($format, 12345.123);
var_dump($string);

$order = array();
for ($i = 0; $i < strlen($string); $i++) {
    $order[] = ord($string[$i]);
}

var_export($order);


var_dump(unpack('f', $string)); 