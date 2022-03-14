<?php

namespace Arithmetic\Math;

use Arithmetic\Math\PrimeFactors\PrimeFactors;
use PHPUnit\Framework\TestCase;

class PrimeFactorsTest extends TestCase
{
    public function testSolution1()
    {
        $primeFactors = new PrimeFactors();
        $this->assertEquals([1], $primeFactors->solution(1));
        $this->assertEquals([7], $primeFactors->solution(7));
        $this->assertEquals([2, 2, 2], $primeFactors->solution(8));
        $this->assertEquals([2, 2, 3, 3, 5], $primeFactors->solution(180));
        $this->assertEquals([2147483647], $primeFactors->solution(2147483647));
        $this->assertEquals([2,3,3,7,11,31,151,331], $primeFactors->solution(2147483646));
    }
}
