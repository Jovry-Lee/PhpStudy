<?php

$input = trim(fgets(STDIN));
$arr = explode(" ", $input);
$len = count($arr);




$min = abs($arr[0] + $arr[1]);
$x = $arr[0];
$y = $arr[1];
for ($i = 0; $i < $len; $i++) {
    for ($j = $i + 1; $j < $len; $j++) {
        if ($min > abs($arr[$i] + $arr[$j])) {
            $min = abs($arr[$i] + $arr[$j]);
            $x = $arr[$i];
            $y = $arr[$j];
        }
    }
}