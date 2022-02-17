<?php

use DataStructure\Linked\Linked;

require_once __DIR__ . "/../../init.php";

$linked = new Linked([5,3,6,1,2]);

// 测试删除.
$linked->delete($linked->head, 6);

// 测试插入.
$linked->insert($linked->head, 3, 4);

// 测试搜索.
$count = $linked->search($linked->head, 5);
var_dump($count);

$l = $linked->head->next;
while (!empty($l))
{
    var_dump($l->data);
    $l = $l->next;
}