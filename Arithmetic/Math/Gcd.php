<?php
/**
 * 最大公约数。
 * 一般用gcd(a,b)表示a,b的最大公约数。
 *
 * 常用解法：欧几里得算法（即辗转相除法）：gcd(a, b) = gcd(b, a%b)
 * 由该定理可知：若a<b, 则定理结果就是将a,b交换；若a>b，则可将数据变小，且减少的速度非常快。
 * 递归式：gcd(a, b) = gcd(b, a%b)
 * 递归边界：gcd(a, 0) = a;(0和任意一个整数a的最大公约数都是a)。
 *
 */
namespace Arithmetic\Math;

class Gcd
{
    /**
     * 欧几里得算法。
     *
     * @param int $a 整数a。
     * @param int $b 整数b。
     *
     * @return int
     */
    public function euclideanAlgorithm(int $a, int $b) :int
    {
        if ($b == 0) {
            return $a;
        } else {
            return $this->euclideanAlgorithm($b, $a % $b);
        }
    }
}