<?php

namespace Tests\Arithmetic\Math;

use Arithmetic\Math\Prime\Prime;
use Arithmetic\Math\Prime\PrimeTable;
use PHPUnit\Framework\TestCase;

class PrimeTest extends TestCase
{
    public function testIsPrime()
    {
        $prime = new Prime();
        $this->assertTrue($prime->isPrime(5));
        $this->assertTrue($prime->isPrimeSimple(5));
        $this->assertFalse($prime->isPrime(4));
        $this->assertFalse($prime->isPrimeSimple(4));
    }

    public function testPrimeTable()
    {
        $primeTable = new PrimeTable();
        $this->assertEquals([2, 3, 5, 7], $primeTable->solution1(10));
        $this->assertEquals([2, 3, 5, 7], $primeTable->solution2(10));
    }
}
