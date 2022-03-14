<?php

/**
 * 58. 最后一个单词长度。
 * @link https://leetcode-cn.com/problems/length-of-last-word/
 */
namespace Arithmetic\LeetCode;

class LengthOfLastWord
{
    public function solution1($str)
    {
       $arr = explode(' ', trim($str));
       return strlen(end($arr));
    }

    public function solution2($str)
    {
        $s = trim($str);
        return strlen(substr($s, strripos($s, ' ') + 1));
    }

    public function solution3($str)
    {
        $i = strlen($str) - 1;
        while ($i >= 0 && $str[$i] == ' ') {
            $i--;
        }

        $j = $i;
        while ($j >= 0 && $str[$j] != ' ')
        {
            $j--;
        }
        return $i - $j;
    }
}

