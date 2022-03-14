<?php

namespace Tests\Arithmetic\NowCoder;

use Arithmetic\NowCoder\HJ5_Hex2Dec;
use PHPUnit\Framework\TestCase;
use Tests\Arithmetic\LeetCode\Util;

class HJ5_Hex2DecTest extends TestCase
{
    use Util;

    private static function inst() :HJ5_Hex2Dec
    {
        return self::getInstance();
    }

    public function testC460()
    {
        $string = '0xC460';
        $this->assertEquals(50272, self::inst()->solution1($string));
        $this->assertEquals(50272, self::inst()->solution2($string));
    }
}
