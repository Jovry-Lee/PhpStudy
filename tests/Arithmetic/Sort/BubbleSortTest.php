<?php

namespace Tests\Arithmetic\Sort;

use Arithmetic\Sort\BubbleSort;
use PHPUnit\Framework\TestCase;

class BubbleSortTest extends TestCase
{
    public function testSort()
    {
        $arr = [3, 1, 4, 5, 2];
        $bubbleSort = new BubbleSort();
        $bubbleSort->sort($arr);
        $this->assertEquals([1, 2, 3, 4, 5], $arr);
    }

    public function testRSort()
    {
        $arr = [3, 1, 4, 5, 2];
        $bubbleSort = new BubbleSort();
        $bubbleSort->rsort($arr);
        $this->assertEquals([5, 4, 3, 2, 1], $arr);
    }
}
