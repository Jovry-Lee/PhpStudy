<?php

require_once "SingletonDb.php";

$db = SingletonDb::getInstance();

// 增.
$result = $db->insert('category', array('name' => '黑土豆', 'parent_id' => 7));
var_dump($result);
// 改.
$result = $db->modify('category', array('name' => '黄皮土豆'), array('name' => '黑土豆'));
var_dump($result);
// 删.
$result= $db->delete('category', array('name' => '黑土豆'));
var_dump($result);
// 查.
$result = $db->query('category', array('parent_id' => 0), array('name'));
// 获取最后一次查询的sql.
var_dump($db->getLastSql());


