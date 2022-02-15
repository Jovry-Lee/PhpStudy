<?php


$string = pack('A6', 'china');
var_dump($string);
echo ord($string[5]);
die;


$string = pack('a6', 'china');
var_dump($string);
echo ord($string[5]);
die;







