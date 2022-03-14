<?php
/**
 * 分数类型。
 *
 * 约定：
 * ①. down为分母，为非负数。若要表示负数，则令分子up为负数；
 * ②. 分数表示0，令分子up为0，分母down为1；
 * ③. 分子和分母没有除了1以外的公约数；
 */
namespace Arithmetic\Math\Fraction;

use Arithmetic\Math\Gcd;

class Fraction
{
    /**
     * @var int $up 分子。
     */
    private int $up;

    /**
     * @var int $down 分母。
     */
    private int $down;

    /**
     * @return int
     */
    public function getDown(): int
    {
        return $this->down;
    }

    /**
     * @param int $down
     *
     * @return Fraction
     */
    public function setDown(int $down): Fraction
    {
        $this->down = $down;
        return $this;
    }

    /**
     * @return int
     */
    public function getUp(): int
    {
        return $this->up;
    }

    /**
     * @param int $up
     *
     * @return Fraction
     */
    public function setUp(int $up): Fraction
    {
        $this->up = $up;
        return $this;
    }

    /**
     * 分数形式展示。
     */
    public function showFraction() :string
    {
        // 整数。
        if ($this->getDown() == 1) {
            return sprintf("%d", $this->getUp());
        }

        // 假分数。
        if (abs($this->getUp()) > $this->getDown()) {
            return sprintf(
                "%d %d/%d",
                $this->getUp() / $this->getDown(),
                $this->getUp() % $this->getDown(),
                $this->getDown()
            );
        }

        // 真分数。
        return sprintf(
            "%d/%d",
            $this->getUp(),
            $this->getDown()
        );
    }

    /**
     * 分数的化简，使得分数满足分数表示的三项约定。
     *
     * ①、若分母down为负数，那么令分子up和分母都变为相反数；
     * ②、若分子up为0，那么令分母down为1；
     * ③、约分：求出分子的绝对值与分母的绝对值的最大公约数d，然后令分子分母同时除以d。
     *
     * @return Fraction
     */
    public function reduction() :Fraction
    {
        // 分母为负数，令分子和分母都变为相反数。
        if ($this->getDown() < 0) {
            $this->setUp(-$this->getUp());
            $this->setDown(-$this->getDown());
        }

        // 若分子为0，则令分母为1；
        if ($this->getUp() == 0) {
            $this->setDown(1);
            return $this;
        }

        // 求约分。
        $gcd = new Gcd();
        $d = $gcd->euclideanAlgorithm(abs($this->getUp()), abs($this->getDown())); // 获取分子、分母的最大公约数。
        $this->setDown($this->getDown() / $d);
        $this->setUp($this->getUp() / $d);
        return $this;
    }
}