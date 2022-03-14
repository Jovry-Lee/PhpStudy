<?php

namespace Arithmetic\Sort;

use PHPUnit\Framework\TestCase;

class QuickSortTest extends TestCase
{
    public function testSort()
    {
        $arr = [35, 18, 16, 72, 24, 65, 12, 88, 46, 28, 55];
        $bubbleSort = new QuickSort();
        $bubbleSort->sort($arr);
        var_export($arr);die;
        $this->assertEquals([12, 16, 18, 24, 28, 35, 46, 55, 65, 72, 88], $arr);
    }
}
