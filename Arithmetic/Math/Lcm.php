<?php
/**
 * 最小公倍数。
 *
 * 一般用lcm(a, b)表示a, b的最小公倍数。
 *
 * lcm(a, b) = (a * b) / d,其中d是a, b的最大公约数。
 */
namespace Arithmetic\Math;


class Lcm
{
    public function lcm(int $a, int $b) :int
    {
        $gcd = new Gcd();
        $d = $gcd->euclideanAlgorithm($a, $b);
        return ($a * $b) / $d;
    }
}