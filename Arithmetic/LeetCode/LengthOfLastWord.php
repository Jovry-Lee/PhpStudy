<?php

/**
 * 58. 最后一个单词长度。
 * @link https://leetcode-cn.com/problems/length-of-last-word/
 */
namespace Arithmetic\LeeCode;


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
        $str = trim($str);
        $index = strlen($str) - 1;
        $lastWorldLen = 0;
        while($index != 0 && $str[$index] != ' ') {
            $lastWorldLen++;
            $index--;
        }
        return $lastWorldLen;
    }
}


//$input = trim(fgets(STDIN));
$input = 'Hello World';
$input = "   fly me   to   the moon  ";
$lengthOfLastWord = new LengthOfLastWord();
$solution1Result = $lengthOfLastWord->solution3($input);
echo "solution1 Result:\t" . $solution1Result . "\n";

