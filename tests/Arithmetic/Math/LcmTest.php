<?php

namespace Tests\Arithmetic\Math;

use Arithmetic\Math\Lcm;
use PHPUnit\Framework\TestCase;

class LcmTest extends TestCase
{
    public function testLcm()
    {
        $a = 4;
        $b = 6;
        $lcm = new Lcm();
        $this->assertEquals(12, $lcm->lcm($a, $b));
    }
}
