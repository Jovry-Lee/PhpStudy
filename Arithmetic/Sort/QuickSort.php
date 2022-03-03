<?php

/**
 * 快速排序。
 *
 * 快排思路：
 * ①、调整序列中的元素，使当前序列最左端的元素在调整后满足左侧所有元素不超过该元素，右侧所有元素均大于该元素。
 * ②、对该元素的左侧和右侧分别递归进行①调整，知道当前调整区间的长度不超过1。
 *
 *
 * 平均时间复杂度：O(NlogN)
 * 最坏时间复杂度：O(N^2)，场景：序列元素接近有序时。
 */
namespace Arithmetic\Sort;


class QuickSort extends Sort
{
    /**
     * @inheritDoc
     */
    public function sort(array &$arr): ?bool
    {
        $this->quickSort($arr, 0, count($arr) - 1);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function rsort(array &$arr): ?bool
    {
        // TODO: Implement rsort() method.
    }

    private function quickSort(array &$arr, int $left, int $right)
    {
        if ($left < $right) {
            $pos = $this->partition($arr, $left, $right);
            // 对左子区间进行快排。
            $this->quickSort($arr, $left, $pos - 1);
            // 对于右子区间进行快排。
            $this->quickSort($arr, $pos + 1, $right);
        }
    }

    /**
     * eg：[35, 18, 16, 72, 24, 65, 12, 88, 46, 28, 55]
     *
     * temp = 35;
     * left=0，right = 10  序列：[ , 18, 16, 72, 24, 65, 12, 88, 46, 28, 55]
     * left=0，right = 9  序列：[28, 18, 16, 72, 24, 65, 12, 88, 46,  , 55]
     * left=3，right = 9  序列：[28, 18, 16,  , 24, 65, 12, 88, 46, 72, 55]
     * left=3，right = 6  序列：[28, 18, 16, 12, 24, 65,  , 88, 46, 72, 55]
     * left=5，right = 6  序列：[28, 18, 16, 12, 24,  , 65, 88, 46, 72, 55]
     * left=5，right = 5  序列：[28, 18, 16, 12, 24, 35, 65, 88, 46, 72, 55]
     *
     * temp写入：35
     */
    private function partition(array &$arr, int $left, int $right) :int
    {
        $temp = $arr[$left];
        while ($left < $right) {
            // 反复左移right。
            while ($left < $right && $arr[$right] > $temp) $right--;
            // 将arr[right] 移到arr[left].
            $arr[$left] = $arr[$right];

            // 反复右移left。
            while ($left < $right && $arr[$left] <= $temp) $left++;
            // 将arr[left]移到$arr[right].
            $arr[$right] = $arr[$left];
        }
        $arr[$left] = $temp;
        var_export($left);
        return $left;
    }
}