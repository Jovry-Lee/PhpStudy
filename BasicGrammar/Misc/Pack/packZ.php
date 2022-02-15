<?php

$string = pack('Z2', 'abc5'); //其实就是将从Z后面的数字位置开始，全部设置为nul
var_dump($string); //输出:string(2) "a"

for($i=0;$i<strlen($string);$i++) {
    echo ord($string[$i]) . PHP_EOL;
}
//输出: 97 0