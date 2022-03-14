<?php
/**
 * 素数表。
 *
 * 获取1~n范围内的素数表的方法。
 */
namespace Arithmetic\Math\Prime;

class PrimeTable
{
    /**
     * 枚举法。
     *
     * 对1~n进行枚举，判断每一个数是否为素数，若为素数，则加入素数表。
     *
     * 复杂度：O(N*sqrt(N)) (即：判断素数的O(sqrt(n)), 枚举部分O(n)).
     *
     * 注：该复杂度对于n不超过10^5的大小是没问题的。
     */
    public function solution1(int $n) :array
    {
        $result = [];
        $prime = new Prime();
        for ($i = 1; $i <= $n; $i++) {
            if ($prime->isPrime($i)) {
                $result[] = $i;
            }
        }
        return $result;
    }

    /**
     * 筛选法。
     *
     * 思路：从小到大枚举所有数，对每一个数，筛去它的所有倍数，剩下的就是素数。
     * （因为若数a不是素数，那么a一定有小于a的素因子。）
     *
     * 时间复杂度：O(NloglogN)
     */
    public function solution2(int $n) :array
    {
        $result = [];

        // 用于标记某个数a是否被筛掉，若p[a]=true, 表示非素数，p[a]=false，表示素数。
        $p = [];
        for ($i = 2; $i <= $n; $i++) {
            if (!($p[$i] ?? false)) {
                $result[] = $i;
                // 过滤其倍数。
                for ($j = $i + $i; $j <= $n; $j += $i) {
                    $p[$j] = true;
                }
            }
        }
        return $result;
    }
}