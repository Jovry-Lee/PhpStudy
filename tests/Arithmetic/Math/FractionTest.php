<?php

namespace Tests\Arithmetic\Math;

use Arithmetic\Math\Fraction\Fraction;
use Arithmetic\Math\Fraction\FractionArithmetic;
use PHPUnit\Framework\TestCase;

class FractionTest extends TestCase
{
    public function testShowFraction()
    {
        $fraction = new Fraction();
        $fraction->setUp(2);
        $fraction->setDown(3);
        $this->assertEquals('2/3', $fraction->showFraction());
    }

    public function testReduction()
    {
        $fraction = new Fraction();
        $fraction->setUp(1)->setDown(-2);
        $this->assertEquals('-1/2', $fraction->reduction()->showFraction());

        $fraction->setUp(8)->setDown(3);
        $this->assertEquals('2 2/3', $fraction->reduction()->showFraction());

        $fraction->setUp(4)->setDown(6);
        $this->assertEquals('2/3', $fraction->reduction()->showFraction());
    }

    public function testAdd()
    {
        $f1 = new Fraction();
        $f1->setUp(2)->setDown(3);

        $f2 = new Fraction();
        $f2->setUp(3)->setDown(2);
        $fractionArithmetic = new FractionArithmetic();
        $this->assertEquals('2 1/6', $fractionArithmetic->add($f1, $f2)->showFraction());
    }

    public function testSub()
    {
        $f1 = new Fraction();
        $f1->setUp(3)->setDown(2);

        $f2 = new Fraction();
        $f2->setUp(2)->setDown(3);
        $fractionArithmetic = new FractionArithmetic();
        $this->assertEquals('5/6', $fractionArithmetic->sub($f1, $f2)->showFraction());
    }

    public function testMul()
    {
        $f1 = new Fraction();
        $f1->setUp(3)->setDown(2);

        $f2 = new Fraction();
        $f2->setUp(2)->setDown(3);
        $fractionArithmetic = new FractionArithmetic();
        $this->assertEquals('1', $fractionArithmetic->mul($f1, $f2)->showFraction());
    }

    public function testDiv()
    {
        $f1 = new Fraction();
        $f1->setUp(3)->setDown(2);

        $f2 = new Fraction();
        $f2->setUp(2)->setDown(3);
        $fractionArithmetic = new FractionArithmetic();
        $this->assertEquals('2 1/4', $fractionArithmetic->div($f1, $f2)->showFraction());
    }
}
