<?php
/**
 * 插入排序。
 *
 * 直接插入排序：指对序列A的N个元素A[1]~A[n],令i从2到n进行枚举，进行n-1趟操作。假设某一趟时，序列A的前[1, i - 1]元素已经有序，而[i, n]未有序，
 * 那么从范围[1, i - 1]中寻找某个位置j，使得将A[i]插入位置j后范围[1, i]有序。
 *
 * 插入排序是将待插入元素一个个插入初始已有序部分中的过程，且插入位置遵循插入后仍然保持有序的原则。
 *
 * eg：[5, 2, 4, 6, 3, 1]
 *
 * 初始有序：5 | 2，4，6，3，1
 * 第一趟：2，5 | 4，6，3，1
 * 第二趟：2，4，5 | 6，3，1
 * 第三趟：2，4，5，6 | 3，1
 * 第四趟：2, 4, 5, 3, 6 | 1  ----> 2, 4, 3, 5, 6 | 1   ---->  2, 3, 4, 5, 6 | 1
 * 第四趟：2，3，4，5，1，6  ----->  2, 3, 4, 1, 5, 6  ----> 2，3，1，4，5，6 ----> 2，1，3，4，5，6  ---->  1，2，3，4，5，6
 */
namespace Arithmetic\Sort;

class InsertSort extends Sort
{
    /**
     * @inheritDoc
     */
    public function sort(array &$arr): ?bool
    {
        $n = count($arr);
        // 进行n - 1趟排序。
        for ($i = 1; $i < $n; $i++) {
            // temp临时存放arr[i]。
            $temp = $arr[$i];
            // j从i开始向前枚举。
            $j = $i;
            while ($j > 0 && $temp < $arr[$j - 1]) { // 只要temp小于前一个元素arr[j-1]，就把arr[j-1]后移以为至arr[j].
                $arr[$j] = $arr[$j - 1];
                $j--;
            }
            $arr[$j] = $temp;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function rsort(array &$arr): ?bool
    {
        $n = count($arr);
        // 进行n - 1趟排序。
        for ($i = 1; $i < $n; $i++) {
            // temp临时存放arr[i]。
            $temp = $arr[$i];
            // j从i开始向前枚举。
            $j = $i;
            while ($j > 0 && $temp > $arr[$j - 1]) { // 只要temp小于前一个元素arr[j-1]，就把arr[j-1]后移以为至arr[j].
                $arr[$j] = $arr[$j - 1];
                $j--;
            }
            $arr[$j] = $temp;
        }

        return true;
    }
}