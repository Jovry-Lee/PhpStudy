<?php

namespace Tests\Arithmetic\Math;

use Arithmetic\Math\BigNumber\BigNumber;
use Arithmetic\Math\BigNumber\BigNumberArithmetic;
use PHPUnit\Framework\TestCase;

class BigNumberArithmeticTest extends TestCase
{
    public function testToString()
    {
        $this->assertEquals('123', (new BigNumber('123'))->toString());
        $this->assertEquals('123', (new BigNumber(['3','2','1']))->toString());
    }

    public function testCompare()
    {
        $bigNumberArithmetic = new BigNumberArithmetic();
        $this->assertEquals(
            1,
            $bigNumberArithmetic->compare(
                new BigNumber('1328'),
                new BigNumber('326')
            )
        );

        $this->assertEquals(
            -1,
            $bigNumberArithmetic->compare(
                new BigNumber('132'),
                new BigNumber('326')
            )
        );

        $this->assertEquals(
            0,
            $bigNumberArithmetic->compare(
                new BigNumber('132'),
                new BigNumber('132')
            )
        );
    }

    public function testAdd()
    {
        $bigNumberArithmetic = new BigNumberArithmetic();
        $this->assertEquals(
            '1654',
            $bigNumberArithmetic->add(
                new BigNumber('1328'),
                new BigNumber('326')
            )->toString()
        );

        $this->assertEquals(
            '0',
            $bigNumberArithmetic->add(
                new BigNumber('0'),
                new BigNumber('0')
            )->toString()
        );

        $this->assertEquals(
            '2469135782469135780',
            $bigNumberArithmetic->add(
                new BigNumber('1234567891234567890'),
                new BigNumber('1234567891234567890')
            )->toString()
        );
    }
}
