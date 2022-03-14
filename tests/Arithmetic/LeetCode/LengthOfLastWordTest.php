<?php
/**
 * @see \Arithmetic\LeetCode\LengthOfLastWord
 */
namespace Tests\Arithmetic\LeetCode;

use PHPUnit\Framework\TestCase;
use \Arithmetic\LeetCode\LengthOfLastWord;

class LengthOfLastWordTest extends TestCase
{
    use Util;

    private static function inst() :LengthOfLastWord
    {
        return self::getInstance();
    }

    public function testHelloWorld()
    {
        $string = 'Hello World';
        $this->assertEquals(5, self::inst()->solution1($string));
        $this->assertEquals(5, self::inst()->solution2($string));
        $this->assertEquals(5, self::inst()->solution3($string));
    }
}
