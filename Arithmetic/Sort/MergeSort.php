<?php
/**
 * 归并排序。
 *
 * 2-路归并排序思想：将序列两两分组，将序列归并为n/2个组，组内单独排序，然后再将这些组两两归并，生成n/4个组，组内再单独排序，以此类推，直到只剩下一个组为止。
 *
 * 时间复杂度：O(NlogN)
 *
 *
 * eg：[66, 12, 33, 57, 64, 27, 18]进行2路归并排序。
 *
 * 第一遍：两两分组，得到四组：[66, 12], [33, 57], [64, 27], [18], 然后组内单独排序：[12, 66], [33, 57], [27, 64], [18].
 * 第二遍：两两分组，得到两组：[12, 66, 33, 57], [27, 64, 18]，然后组内单独排序：[12, 33, 57, 66], [18, 27, 64].
 * 第三遍：两两分组，得到一组：[12, 33, 57, 66, 18, 27, 64], 然后组内单独排序：[12, 18, 27, 33, 57, 64, 66].
 */
namespace Arithmetic\Sort;

class MergeSort extends Sort
{
    /**
     * @inheritDoc
     */
    public function sort(array &$arr): ?bool
    {
        $this->mergeSort($arr, 0, count($arr) - 1);
        return true;
    }

    /**
     * 将arr数组当前区间[left, right]进行归并排序。
     */
    private function mergeSort(array &$arr, int $left, int $right) :void
    {
        // 只要left小于right。
        if ($left < $right) {
            // 取left和right的中间值。
            $mid = (int)($left + $right) / 2;

            // 递归，将左子区间[left，mid]归并排序。
            $this->mergeSort($arr, $left, $mid);

            // 将右子区间[mid + 1, right]归并排序。
            $this->mergeSort($arr, $mid + 1, $right);

            // 将左右子区间进行合并。
            $this->merge($arr, $left, $mid, $mid + 1, $right);
        }
    }

    /**
     * 将$arr的[l1, r1], [l2, r2]区间合并为有序区间。
     *
     */
    private function merge(array &$arr, int $l1, int $r1, int $l2, int $r2) :void
    {
        $i = $l1;
        $j = $l2;
        $temp = [];
        while ($i <= $r1 && $j <= $r2) {
            if ($arr[$i] <= $arr[$j]) { // 若arr[i]<arr[j],将arr[i]加入temp数组。
                $temp[] = $arr[$i++];
            } else { // 若arr[i]>arr[j],将arr[j]加入temp数组。
                $temp[] = $arr[$j++];
            }
        }

        // 将[l1, r1]中剩余元素加入temp数组中。
        while ($i <= $r1) $temp[] = $arr[$i++];

        // 将[l2, r2]中剩余元素加入temp数组中。
        while ($j <= $r2) $temp[] = $arr[$j++];

        // 将合并后的序列赋值回数组arr.
        for ($i = 0; $i < count($temp); $i++) {
            $arr[$l1 + $i] = $temp[$i];
        }
    }

    /**
     * @inheritDoc
     */
    public function rsort(array &$arr): ?bool
    {
        $this->rMergeSort($arr, 0, count($arr) - 1);
        return true;
    }

    /**
     * 将arr数组当前区间[left, right]进行归并排序。
     */
    private function rMergeSort(array &$arr, int $left, int $right) :void
    {
        // 只要left小于right。
        if ($left < $right) {
            $mid = (int)($left + $right) / 2;  // 取left和right的中间值。
            $this->rMergeSort($arr, $left, $mid); // 递归，将左子区间[left，mid]归并排序。
            $this->rMergeSort($arr, $mid + 1, $right); // 将右子区间[mid + 1, right]归并排序。
            $this->rMerge($arr, $left, $mid, $mid + 1, $right); // 将左右子区间进行合并。
        }
    }

    /**
     * 将$arr的[l1, r1], [l2, r2]区间合并为有序区间。
     *
     */
    private function rMerge(array &$arr, int $l1, int $r1, int $l2, int $r2) :void
    {
        $i = $l1;
        $j = $l2;
        $temp = [];
        while ($i <= $r1 && $j <= $r2) {
            if ($arr[$i] >= $arr[$j]) { // 若arr[i]>=arr[j],将arr[i]加入temp数组。
                $temp[] = $arr[$i++];
            } else { // 若arr[i]<arr[j],将arr[j]加入temp数组。
                $temp[] = $arr[$j++];
            }
        }

        // 将[l1, r1]中剩余元素加入temp数组中。
        while ($i <= $r1) $temp[] = $arr[$i++];

        // 将[l2, r2]中剩余元素加入temp数组中。
        while ($j <= $r2) $temp[] = $arr[$j++];

        // 将合并后的序列赋值回数组arr.
        for ($i = 0; $i < count($temp); $i++) {
            $arr[$l1 + $i] = $temp[$i];
        }
    }
}