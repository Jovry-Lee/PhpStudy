<?php

namespace Tests\Arithmetic\Sort;

use Arithmetic\Sort\InsertSort;
use PHPUnit\Framework\TestCase;

class InsertSortTest extends TestCase
{
    public function testSort()
    {
        $arr = [3, 1, 4, 5, 2];
        $bubbleSort = new InsertSort();
        $bubbleSort->sort($arr);
        $this->assertEquals([1, 2, 3, 4, 5], $arr);
    }

    public function testRSort()
    {
        $arr = [3, 1, 4, 5, 2];
        $bubbleSort = new InsertSort();
        $bubbleSort->rsort($arr);
        $this->assertEquals([5, 4, 3, 2, 1], $arr);
    }
}
