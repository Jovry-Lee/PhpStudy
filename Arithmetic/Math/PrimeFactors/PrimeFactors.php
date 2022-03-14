<?php
/**
 * 质因子分解。
 *
 * 对一个正整数n来说，如果它存在[2, n]范围内的质因子，那么这些质因子全部小于等于sqrt(n),要么只存在一个大于sqrt(n)的质因子，而七月质因子全部小于等于sqrt(n)。
 * 思路：
 * ①、枚举1~sqrt(n)范围内的所有质数因子p，判断p是否是n的因子，只要p是n的因子，就让n不断除以p，每次操作令p的个数加1，直到p不再是n的因子。
 * ②、若经过步骤①后，n仍然大于1，说明n中有且仅有一个大于sqrt(n)的质因子，此时需将其加入质因子，并令其个数为1.
 */
namespace Arithmetic\Math\PrimeFactors;

class PrimeFactors
{
    public function solution(int $n)
    {
        if ($n == 1) {
            return [1];
        }

        $result = [];
        $num = $n;
        for ($i = 2; $i * $i <= $n; $i++) {
            while ($num % $i == 0) {
                $result[] = $i;
                $num /= $i;
            }
        }
        if ($num > 1) {
            $result[] = $num;
        }

        return $result;
    }
}