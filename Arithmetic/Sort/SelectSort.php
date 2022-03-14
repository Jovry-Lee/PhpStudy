<?php
/**
 * 选择排序。
 *
 * 简单选择排序：指对一个序列A中的元素A[1]~A[n]，令i从1到n枚举，进行n趟操作，每趟从待排序部分[i, n]中选择最值元素，
 * 令其与待排序数组的第一个元素A[i]进行交换,这样元素A[i]就会与当前有序区间[i, i - 1]形成新的有序区间[1, i]。
 */
namespace Arithmetic\Sort;

class SelectSort extends Sort
{
    /**
     * @inheritDoc
     */
    public function sort(array &$arr): ?bool
    {
        $n = count($arr);
        // 进行n趟操作。
        for ($i = 0; $i < $n; $i++) {
            $k = $i;
            for ($j = $i; $j < $n; $j++) {
                if ($arr[$j] < $arr[$k]) {
                    $k = $j;
                }
            }

            $this->swap($arr, $i, $k);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function rsort(array &$arr): ?bool
    {
        $n = count($arr);
        // 进行n趟操作。
        for ($i = 0; $i < $n; $i++) {
            $k = $i;
            for ($j = $i; $j < $n; $j++) {
                if ($arr[$j] > $arr[$k]) {
                    $k = $j;
                }
            }

            $this->swap($arr, $i, $k);
        }

        return true;
    }
}