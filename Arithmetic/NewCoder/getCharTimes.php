<?php

/**
 * HJ2 计算某字符出现的次数
 * @link https://www.nowcoder.com/practice/a35ce98431874e3a820dbe4b2d0508b1?tpId=37&tqId=21225&rp=1&ru=/ta/huawei&qru=/ta/huawei&difficulty=&judgeStatus=&tags=/question-ranking
 */

function solution1($string, $char)
{
    $string = strtolower($string);
    $char = strtolower($char);
    return substr_count($string, $char);
}

function solution2($string, $char)
{
    $string = strtolower($string);
    $char = strtolower($char);

    $length = strlen($string);
    $ans = 0;
    for($i = 0; $i < $length; $i++) {
        if ($string[$i] == $char[0]) {
            $ans++;
        }
    }

}

$string = 'ABCabc';
$char = 'A';
echo solution1($string, $char);
