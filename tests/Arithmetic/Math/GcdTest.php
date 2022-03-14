<?php

namespace Tests\Arithmetic\Math;

use Arithmetic\Math\Gcd;
use PHPUnit\Framework\TestCase;

class GcdTest extends TestCase
{
    public function testEuclidean()
    {
        $a = 49;
        $b = 14;
        $gcd = new Gcd();
        $this->assertEquals(7, $gcd->euclideanAlgorithm($a, $b));
    }
}
