<?php
/**
 * 素数
 *
 * 素数（质数）：指除了1和本身外，不能被其他数整除的一类数。（即给定正整数n，若对任意的正整数a(1<a<n)，都有n%a!=0成立）
 * 合数：给定正整数n，若对任意的正整数a(1<a<n)，存在n%a==0成立.
 *
 * 注：1既不是素数也不是合数。
 *
 * 算法：
 * 1. 判定2、3、...、n-1都不能被n整除，则n为素数。复杂度为：O(n)。
 * 2. 判定2、3、...、[sqrt(n)]都不能被n整除，则n为素数。复杂度为：O(sqrt(n))。算法推导详见：《算法笔记.胡凡》5.4.1章。
 */
namespace Arithmetic\Math\Prime;

class Prime
{
    /**
     * 判断是否是素数。
     * @param int $n 待判定的整数。
     * @return bool
     */
    public function isPrime(int $n) :bool
    {
        if ($n <= 1) {
            return false;
        }

        $sqrt = sqrt($n);
        for ($i = 2; $i <= $sqrt; $i++) {
            if ($n % $i == 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * 判断是否是素数(简洁版)。
     *
     * 注意：此法若$i*$i超过int型变量范围上线，可能导致溢出，造成bug。理论上n在10^9内都是安全的。
     *
     * @param int $n 待判定的整数。
     *
     * @return bool
     */
    public function isPrimeSimple(int $n) :bool
    {
        if ($n <= 1) {
            return false;
        }

        for ($i = 2; $i * $i <= $n; $i++) {
            if ($n % $i == 0) {
                return false;
            }
        }
        return true;
    }
}