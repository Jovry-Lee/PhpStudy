<?php

/**
 * 题目:现在只有两只杯子，容量分别是：5升和7升，问题是：在只用这两个杯子的前提下，如何才能得到4升水？假设：水可以无限使用。
 */
class WaterPouring
{
    /** @var integer $smallCup 小杯子容量*/
    private $smallCup;

    /** @var integer $bigCup 大杯子容量*/
    private $bigCup;

    /** @var integer $target 目标水量.*/
    private $target;

    public function __construct($cut1, $cut2, $target)
    {
        if ($cut1 <= $cut2) {
            $this->smallCup = $cut1;
            $this->bigCup = $cut2;
        } else {
            $this->smallCup = $cut2;
            $this->bigCup = $cut1;
        }
        $this->target = $target;
    }

    public function getWaterPouringWay()
    {
        // 因为每次都是小贝子装满水往大杯子中倒水, 倒完后小杯子剩余水量总是0.
        // 所以只需要跟踪大杯子剩余水量即可知道整个倒水的操作过程.
        echo "初始情况: smallCup: {$this->smallCup}, bigCup: 0\n";

        $count = 1;
        while (true) {
            $smallCup = $count * $this->smallCup;
            $bigCupRemain = $smallCup % $this->bigCup;
            $smallCupRemain = $smallCup / $this->bigCup;
            echo "第{$count}次倒水, smallCup: $smallCupRemain, bigCupRemain: $bigCupRemain";

            if () {

            }
        }

    }
}