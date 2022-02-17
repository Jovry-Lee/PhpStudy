<?php
require_once __DIR__ . "/../../init.php";

use DataStructure\Heap\Heap;

//  测试通过已有的数组建堆.（注: 下标应从1开始.）
$insertArr = [
    1=>85,
    2=>55,
    3=>82,
    4=>57,
    5=>68,
    6=>92,
    7=>99,
    8=>98
];

$heap = new Heap($insertArr);
var_dump("堆元素: " . json_encode($heap->heap) . "\n");


// 测试删除堆顶元素.
$heap->deleteTop();
var_dump("删除堆顶元素后的堆数据: " . json_encode($heap->heap) . "\n");


// 测试堆递增排序.
$heap->heapSort();
var_dump("递增排序后的堆数据: " . json_encode($heap->heap) . "\n");


// 测试往堆中插入数据.
$heap->insert(75);
var_dump("新数据插入后的堆数据：" . json_encode($heap->heap));
