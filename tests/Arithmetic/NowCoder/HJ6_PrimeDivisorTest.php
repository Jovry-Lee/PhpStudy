<?php

namespace Tests\Arithmetic\NowCoder;

use Arithmetic\NowCoder\HJ6_PrimeDivisor;
use PHPUnit\Framework\TestCase;
use Tests\Arithmetic\LeetCode\Util;

class HJ6_PrimeDivisorTest extends TestCase
{
    use Util;

    private static function inst() :HJ6_PrimeDivisor
    {
        return self::getInstance();
    }

    public function test180()
    {
        $num = 180;
        $this->assertEquals([2,2,3,3,5], self::inst()->solution1($num));
        $this->assertEquals([2,2,3,3,5], self::inst()->solution2($num));
    }
}
