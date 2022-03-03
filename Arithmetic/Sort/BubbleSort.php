<?php
/**
 * 冒泡排序。
 *
 * 冒泡排序的本质在于交换，即每次通过交换的方式将当前剩余元素的最大/小值移动到一端，当剩余元素减少为0时，排序结束。
 */
namespace Arithmetic\Sort;

class BubbleSort extends Sort
{
    public function sort(array &$arr): ?bool
    {
        $n = count($arr);
        // n - 1趟循环。
        for ($i = 1; $i <= $n - 1; $i++)
        {
            // 第$i趟时，$arr[0]到$arr[n - i - 1]数据与其下一个数据进行比较。
            for ($j = 0; $j < $n - $i; $j++) {
                if ($arr[$j] > $arr[$j + 1]) {
                    $this->swap($arr, $j, $j + 1);
                }
            }
        }

        return true;
    }

    public function rsort(array &$arr): ?bool
    {
        $n = count($arr);
        // n - 1趟循环。
        for ($i = 1; $i <= $n - 1; $i++)
        {
            // 第$i趟时，$arr[0]到$arr[n - i - 1]数据与其下一个数据进行比较。
            for ($j = 0; $j < $n - $i; $j++) {
                if ($arr[$j] < $arr[$j + 1]) {
                    $this->swap($arr, $j, $j + 1);
                }
            }
        }

        return true;
    }
}