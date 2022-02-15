<?php

namespace DataStructure\Tree\Heap;


class Heap
{
    public $heap = array();
    public $n = 0; // 元素个数.

    /**
     * 对heap数组在[low, high]范围内进行向下调整.
     *
     * @param integer $low  想要调整结点的数组下标.
     * @param integer $high 堆中最后一个元素的数组下标.
     *
     * @return void
     */
    public function downAdjust($low, $high)
    {
        $i = $low; // i为欲调整的结点.
        $j = $i * 2; // j为其左孩子.
        while ($j <= $high) { // 存在子结点.
            // 若右子结点存在,且右子结点的值大于左结点的值.
            if (($j + 1 <= $high) && ($this->heap[$j + 1] > $this->heap[$j])) {
                // 让j存储右子结点的下标.
                $j++;
            }

            // 若子结点中最大的权值比欲调整结点i大.
            if ($this->heap[$j] > $this->heap[$i]) {
                // 交换.
                $temp = $this->heap[$j];
                $this->heap[$j] = $this->heap[$i];
                $this->heap[$i] = $temp;

                // 保持i为欲调整结点,j为i的左子结点.
                $i = $j;
                $j = $i * 2;
            } else {
                break; // 子结点的权值均比欲调整结点i小,调整结束.
            }

        }
    }

    /**
     * 对heap数组在[low, high]范围内进行向上调整.
     *
     * @param integer $low  一般设置为1.
     * @param integer $high 欲调整结点的数组下标.
     *
     * @return void
     */
    public function upAdjust($low, $high)
    {
        $i = $high; // i为欲调整的结点.
        $j = bcdiv($i, 2); // j为其父结点.
        while ($j >= $low) { // 父结点在[low, high]范围内.
            // 父结点权值小于欲调整结点i的权值.
            if ($this->heap[$j] < $this->heap[$i]) {
                // 交换父结点和调整结点.
                $temp = $this->heap[$j];
                $this->heap[$j] = $this->heap[$i];
                $this->heap[$i] = $temp;

                // 保持i为欲调整结点,j为i的父结点.
                $i = $j;
                $j = bcdiv($i, 2);
            } else {
                break; // 父结点权值比结点i的权值大,调整结束.
            }

        }
    }

    /**
     * 建堆.
     */
    public function createHeap()
    {
        for ($i = bcdiv($this->n, 2); $i >= 1; $i--){
            $this->downAdjust($i, $this->n);
        }
    }

    /**
     * 删除堆顶元素.
     */
    public function deleteTop()
    {
        // 用最后元素覆盖堆顶元素, 并让元素个数减1.
        $this->heap[1] =$this->heap[$this->n--];
        //  向下调整堆顶元素.
        $this->downAdjust(1, $this->n);
    }

    /**
     * 添加元素.
     *
     * @param mixed $x 待添加的元素权值.
     */
    public function insert($x)
    {
        // 让元素个数加1, 然后将数组末尾赋值为x.
        $this->heap[++$this->n] = $x;
        // 向上调整新加入的结点n.
        $this->upAdjust(1, $this->n);
    }

    /**
     * 递增排序.
     */
    public function heapSort()
    {
        // 建堆.
        $this->createHeap();
        for ($i = $this->n; $i > 1; $i--) {
            // 交换heap[i]与堆顶.
            $temp = $this->heap[$i];
            $this->heap[$i] = $this->heap[1];
            $this->heap[1] = $temp;

            // 调整堆顶.
            $this->downAdjust(1, $i - 1);
        }
    }

}

$heap = new Heap();

//  测试通过已有的数组建堆.
// 注: 下标应从1开始.
$insertArr = array(1=>85, 2=>55, 3=>82, 4=>57, 5=>68, 6=>92, 7=>99, 8=>98);

$heap->heap = $insertArr;
$heap->n = count($insertArr);

//$heap->createHeap();
//var_dump("堆元素: " . json_encode($heap->heap) . "\n");
//
//// 测试删除堆顶元素.
//$heap->deleteTop();
//var_dump("删除堆顶元素后的堆数据: " . json_encode($heap->heap) . "\n");


// 测试堆递增排序.
$heap->heapSort();
var_dump("递增排序后的数组: " . json_encode($heap->heap) . "\n");


// 测试往堆中插入数据.
//foreach ($insertArr as $x) {
//    $heap->insert($x);
//}
//var_dump(json_encode($heap->heap));

