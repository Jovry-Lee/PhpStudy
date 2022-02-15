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

        // 获取第一次倒水的情况,防止循环.
        $flag = $this->smallCup % $this->bigCup;
        if ($flag == $this->target) {
            echo "第1次倒水, smallCup: $this->smallCup, bigCupRemain: $flag\n";
            echo "倒水成功, 得到目标水量!\n";
            return;
        }

        $count = 2;
        while (true) {
            $smallCup = $count * $this->smallCup;
            $bigCupRemain = $smallCup % $this->bigCup;
            echo "第{$count}次倒水, smallCup: $smallCup, bigCupRemain: $bigCupRemain\n";

            if ($bigCupRemain == $this->target) {
                echo "倒水成功, 得到目标水量!\n";
                break;
            } elseif ($bigCupRemain == $flag) {
                echo "倒水失败, 无法得到目标水量!\n";
                break;
            }
            $count++;
        }

    }
}

echo "请输入两个杯子的容量:\n";
$cups = array();
for ($i = 0; $i < 2; $i++) {
    $cups[] = intval(trim(fgets(STDIN)));
}

echo "请输入目标水量: \n";
$target = intval(trim(fgets(STDIN)));

$waterPouring = new WaterPouring($cups[0], $cups[1], $target);
$waterPouring->getWaterPouringWay();
