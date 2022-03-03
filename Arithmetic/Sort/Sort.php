<?php
/**
 * 基础排序类。
 */
namespace Arithmetic\Sort;

abstract class Sort implements ISort
{
    /**
     * 交换数组元素。
     * @param $arr
     * @param $i
     * @param $j
     */
    protected function swap(array &$arr, int $i, int $j) :void
    {
        $temp = $arr[$i];
        $arr[$i] = $arr[$j];
        $arr[$j] = $temp;
    }
}