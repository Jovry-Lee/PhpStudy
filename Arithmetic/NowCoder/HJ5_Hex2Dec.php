<?php
/**
 * HJ5 进制转换.
 * @link https://www.nowcoder.com/practice/8f3df50d2b9043208c5eed283d1d4da6?tpId=37&tqId=21228&rp=1&ru=/ta/huawei&qru=/ta/huawei&difficulty=&judgeStatus=&tags=/question-ranking
 */
namespace Arithmetic\NowCoder;

class HJ5_Hex2Dec
{
    /**
     * 通过PHP原生进制转换函数进行转换。
     */
    public function solution1($str)
    {
        return hexdec($str);
    }

    /**
     * 模拟实现。
     */
    public function solution2($str)
    {
        $map = [
            '0' => 0,
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            'a' => 10,
            'b' => 11,
            'c' => 12,
            'd' => 13,
            'e' => 14,
            'f' => 15
        ];

        $hexStr = substr($str, 2);
        $length = strlen($hexStr);

        $result = 0;
        $index = 0;
        while ($length > $index) {
            $result += pow(16, $length - $index - 1) * intval($map[strtolower($hexStr[$index])]);
            $index++;
        }

        return $result;
    }
}