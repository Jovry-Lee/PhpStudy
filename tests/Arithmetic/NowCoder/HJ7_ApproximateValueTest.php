<?php

namespace Tests\Arithmetic\NowCoder;

use Arithmetic\NowCoder\HJ7_ApproximateValue;
use PHPUnit\Framework\TestCase;
use Tests\Arithmetic\LeetCode\Util;

class HJ7_ApproximateValueTest extends TestCase
{
    use Util;

    private static function inst() :HJ7_ApproximateValue
    {
        return self::getInstance();
    }

    public function testGreat()
    {
        $num = 5.6;
        $this->assertEquals(6, self::inst()->solution1($num));
    }

    public function testLess()
    {
        $num = 5.3;
        $this->assertEquals(5, self::inst()->solution1($num));
    }
}
