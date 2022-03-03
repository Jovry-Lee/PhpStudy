<?php

namespace Tests\Arithmetic\Hash;

use Arithmetic\Hash\StringHash;
use PHPUnit\Framework\TestCase;

class StringHashTest extends TestCase
{
    public function testCharToHash()
    {
        $str = 'aBc';
        $stringHash = new StringHash();
        $this->assertEquals(
            26 * 52 * 52 + 52 + (26 + 2),
            $stringHash->charToHash($str)
        );
    }
}
