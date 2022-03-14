<?php

namespace Arithmetic\Math\Fraction;

use Arithmetic\Math\Gcd;

class FractionArithmetic
{
    /**
     * 分数的化简，使得分数满足分数表示的三项约定。
     *
     * ①、若分母down为负数，那么令分子up和分母都变为相反数；
     * ②、若分子up为0，那么令分母down为1；
     * ③、约分：求出分子的绝对值与分母的绝对值的最大公约数d，然后令分子分母同时除以d。
     *
     * @param Fraction $result 待化简的分数。
     *
     * @return Fraction
     */
    public function reduction(Fraction $result) :Fraction
    {
        // 分母为负数，令分子和分母都变为相反数。
        if ($result->getDown() < 0) {
            $result->setUp(-$result->getUp());
            $result->setDown(-$result->getDown());
        }

        // 若分子为0，则令分母为1；
        if ($result->getUp() == 0) {
            $result->setDown(1);
            return $result;
        }

        // 求约分。
        $gcd = new Gcd();
        $d = $gcd->euclideanAlgorithm(abs($result->getUp()), abs($result->getDown())); // 获取分子、分母的最大公约数。
        $result->setDown($result->getDown() / $d);
        $result->setUp($result->getUp() / $d);
        return $result;
    }

    /**
     * 分数的加法。
     * 对于两个分数f1,f2，其加法公式为：result = (f1.up * f2.down + f2.up * f1.down)/(f1.down * f2.down)。
     *
     * @param Fraction $f1 分数1.
     * @param Fraction $f2 分数2.
     *
     * @return Fraction
     */
    public function add(Fraction $f1, Fraction $f2) :Fraction
    {
        $result = new Fraction();
        $result->setUp($f1->getUp() * $f2->getDown() + $f2->getUp() * $f1->getDown());
        $result->setDown($f1->getDown() * $f2->getDown());
        return $this->reduction($result);
    }

    /**
     * 分数的减法。
     * 对于两个分数f1,f2，其减法公式为：result = (f1.up * f2.down - f2.up * f1.down)/(f1.down * f2.down)
     *
     * @param Fraction $f1 分数1.
     * @param Fraction $f2 分数2.
     *
     * @return Fraction
     */
    public function sub(Fraction $f1, Fraction $f2) :Fraction
    {
        $result = new Fraction();
        $result->setUp($f1->getUp() * $f2->getDown() - $f2->getUp() * $f1->getDown());
        $result->setDown($f1->getDown() * $f2->getDown());
        return $this->reduction($result);
    }

    /**
     * 分数的乘法。
     * 对于两个分数f1,f2，其乘法公式为：result = (f1.up * f2.up)/(f1.down * f2.down)
     *
     * @param Fraction $f1 分数1.
     * @param Fraction $f2 分数2.
     *
     * @return Fraction
     */
    public function mul(Fraction $f1, Fraction $f2) :Fraction
    {
        $result = new Fraction();
        $result->setUp($f1->getUp() * $f2->getUp());
        $result->setDown($f1->getDown() * $f2->getDown());
        return $this->reduction($result);
    }

    /**
     * 分数的除法。
     * 对于两个分数f1,f2，其除法公式为：result = (f1.up * f2.down)/(f1.down * f2.up)
     *
     * @param Fraction $f1 分数1.
     * @param Fraction $f2 分数2.
     *
     * @return Fraction
     */
    public function div(Fraction $f1, Fraction $f2) :Fraction
    {
        $result = new Fraction();
        $result->setUp($f1->getUp() * $f2->getDown());
        $result->setDown($f1->getDown() * $f2->getUp());
        return $this->reduction($result);
    }
}