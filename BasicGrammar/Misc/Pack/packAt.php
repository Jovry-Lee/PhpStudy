<?php

$string = pack('@4'); //我理解为填充N个nul
var_dump($string); //输出: string(4) ""

for($i=0;$i<strlen($string);$i++) {
    echo ord($string[$i]) . PHP_EOL;
}