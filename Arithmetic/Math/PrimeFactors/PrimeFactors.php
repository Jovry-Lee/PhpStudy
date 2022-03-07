<?php
/**
 * 质因子分解。
 *
 * 对一个正整数n来说，如果它存在[2, n]范围内的质因子，那么这些质因子全部小于等于sqrt(n),要么只存在一个大于sqrt(n)的质因子，而七月质因子全部小于等于sqrt(n)。
 * 思路：
 * ①、
 *
 */
namespace Arithmetic\Math\PrimeFactors;

class PrimeFactors
{
    public function solution(int $n)
    {
        if ($n == 1) {
            return 1;
        }
    }
}