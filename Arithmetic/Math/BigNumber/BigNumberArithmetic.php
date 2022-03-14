<?php


namespace Arithmetic\Math\BigNumber;


class BigNumberArithmetic
{
    /**
     * 比较大小。
     * @param BigNumber $a 大整数1。
     * @param BigNumber $b 大整数2。
     * @return int
     */
    public function compare(BigNumber $a, BigNumber $b) :int
    {
        if ($a->getLen() > $b->getLen()) {
            return 1;
        } elseif ($a->getLen() < $b->getLen()) {
            return -1;
        }

        // 从高位开始比较。
        $aData = $a->getD();
        $bData = $b->getD();
        for ($i = $a->getLen() -1; $i >= 0; $i--) {
            if (empty($bData[$i]) || intval($aData[$i]) > intval($bData[$i])) {
                return 1;
            } elseif (intval($aData[$i]) < intval($bData[$i])) {
                return -1;
            }
        }

        // 相等。
        return 0;
    }

    /**
     * 大整数的加法。
     *
     * @param BigNumber $a 加数。
     * @param BigNumber $b 被加数。
     *
     * @return BigNumber 和
     * @throws \Exception
     */
    public function add(BigNumber $a, BigNumber $b) :BigNumber
    {
        $result = [];

        $length = max($a->getLen(), $b->getLen());
        $aData = $a->getD();
        $bData = $b->getD();
        $carry = 0;
        for ($i = 0; $i < $length; $i++) {
            $sum = $carry + ($aData[$i] ?? 0) + ($bData[$i] ?? 0);
            $result[] = $sum % 10;
            $carry = intval($sum / 10);
        }

        if ($carry > 0) {
            $result[] = $carry;
        }

        return new BigNumber($result);
    }
}