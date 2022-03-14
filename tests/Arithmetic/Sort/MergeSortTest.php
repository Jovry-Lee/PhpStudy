<?php

namespace Tests\Arithmetic\Sort;

use Arithmetic\Sort\MergeSort;
use PHPUnit\Framework\TestCase;

class MergeSortTest extends TestCase
{
    public function testSort()
    {
        $arr = [66, 12, 33, 57, 64, 27, 18];
        $bubbleSort = new MergeSort();
        $bubbleSort->sort($arr);
        $this->assertEquals([12, 18, 27, 33, 57, 64, 66], $arr);
    }

    public function testRSort()
    {
        $arr = [66, 12, 33, 57, 64, 27, 18];
        $bubbleSort = new MergeSort();
        $bubbleSort->rsort($arr);
        $this->assertEquals([66, 64, 57, 33, 27, 18, 12], $arr);
    }
}
