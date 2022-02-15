<?php

/**
 * 题目: 馒头和奶昔是对dog夫妻，它们有两个baby：淘淘和皮皮。一天晚上，它们一家四口出去游玩，回家过程中迷路了，困在了一座桥边上。馒头家只带了一个手电筒，由于是荒山夜晚，没有手电筒谁也不敢私自过桥。桥呢又比较窄，一次最多只能过2只狗。馒头家每个成员单独过桥所需要的时间依次是：1min、2min、5min、8min。如果两个人一起过桥，需耗费的时间是走得慢的那个成员所需要的时间。馒头一家都想早点过桥回家，现在请你帮助它们设计一个过桥方案，使的总耗费时间最少。馒头脑子不太好用，希望你能够给ta一个通用的方案，以便下次和朋友出去玩时使用，即帮忙给出一个适用任何成员数量的最优过桥方案。注：每个成员单独过桥的时间是已知的。
 */
class GoBridge
{
    /** @var integer $totalNums 总的过桥人数.*/
    private $totalNums;

    /** @var array $times 每个人的过桥时间.*/
    private $times = array();

    /**
     * 结构函数.
     *
     * @param integer $totalNums 总的过桥人数.
     * @param array   $times      每个人的过桥时间.
     */
    public function __construct($totalNums, $times)
    {
        $this->totalNums = $totalNums;
        $this->times = $times;
    }

    /**
     * 获取最小过桥时间.
     */
    public function getMinGoBridgeTime()
    {
        $memberNums = $this->totalNums;
        $memberTimes = $this->times;

        // 排序, 保持索引关系升序排列.
        sort($memberTimes);

        $result = 0;
        $index = $memberNums - 1;

        // 人数大于3的情况.
        // 指向最慢的成员, 先处理最慢的成员.
        for (; $index > 2; $index -= 2) {
            // 最快的依次将最慢的两个人送过桥, 并回去.
            // 1. 最快的和最慢的一期过桥,花费时间为: $memberTimes[$index].
            // 2. 最快的回去, 花费时间为$memberTimes[0].
            // 3. 最快的和次慢的一期过桥,花费时间为: $memberTimes[$index - 1].
            // 4. 最快的回去, 花费时间为$memberTimes[0].
            $t1 = 2 * $memberTimes[0] + $memberTimes[$index] + $memberTimes[$index -1];

            // 1. 最快的两个人一期过桥, 花费时间为$memberTimes[1];
            // 2. 最快的回来, 花费时间为$memberTimes[0];
            // 3. 最慢的两个过桥, 花费时间为$memberTimes[$index];
            // 4. 次快的回去,花费时间为$memberTimes[1]
            $t2 = $memberTimes[0] + 2 * $memberTimes[1] + $memberTimes[$index];

            if ($t1 <= $t2) {
                $result += $t1;
            } else {
                $result += $t2;
            }
            //不管哪一种方案，执行完之后最慢的2个人都过桥了
            //所以for循环中i每次后 减2
        }

        // 人数等于3的情况.
        if ($index == 2) {
            $result += $memberTimes[0] + $memberTimes[1] + $memberTimes[2];
        } elseif ($index == 1) {
            $result += $memberTimes[1];
        } else {
            $result += $memberTimes[0];
        }
        return $result;
    }
}


echo "请输入总人数: ";
$memberNums = intval(trim(fgets(STDIN)));

if (empty($memberNums)) {
    die;
}

echo "请输入上面每个成员的过桥时间: \n";
$memberTimes = array();
for ($i = 0; $i < $memberNums; $i++) {
    $memberTimes[$i] = trim(fgets(STDIN));
}


$goBridge = new GoBridge($memberNums, $memberTimes);
$result = $goBridge->getMinGoBridgeTime();

echo "成员过桥最少花费时间是: $result\n";





