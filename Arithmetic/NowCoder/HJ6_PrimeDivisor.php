<?php

/**
 * HJ6 质数因子
 * @link https://www.nowcoder.com/practice/196534628ca6490ebce2e336b47b3607?tpId=37&tqId=21229&rp=1&ru=/ta/huawei&qru=/ta/huawei&difficulty=&judgeStatus=&tags=/question-ranking
 */
namespace Arithmetic\NowCoder;

use Arithmetic\Util;

class HJ6_PrimeDivisor
{
    public function solution1($num)
    {
        $result = [];
        $primeNumber = 2;
        while ($num != 1) {
            if ($num % $primeNumber == 0) {
                $num = intval($num / $primeNumber);
                $result[] = $primeNumber;
                continue;
            }

            // 计算下一个质数因子。
            while (!Util::isPrime(++$primeNumber));
        }
        return $result;
    }


    public function solution2($num)
    {
        $result = [];
        for ($i = 2; $i * $i <= $num; $i++) {
            while ($num % $i == 0) {
                $result[] = $i;
                $num = intval($num / $i);
            }
        }

        if ($num - 1) {
            $result[] = $num;
        }
        return $result;
    }
}