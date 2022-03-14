<?php

/**
 * HJ7 取近似值。
 * @link https://www.nowcoder.com/practice/3ab09737afb645cc82c35d56a5ce802a?tpId=37&tqId=21230&rp=1&ru=/ta/huawei&qru=/ta/huawei&difficulty=&judgeStatus=&tags=/question-ranking
 */
namespace Arithmetic\NowCoder;

class HJ7_ApproximateValue
{
    public function solution1(float $num)
    {
        return intval($num + 0.5);
    }
}