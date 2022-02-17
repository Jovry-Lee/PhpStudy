<?php

require_once __DIR__ . "/../../../init.php";

use \DataStructure\Tree\BinarySearchTree\BinarySearchTree;

$bst = new BinarySearchTree();
$node = null;
$data = array(15, 10, 8, 12, 20, 17, 25, 19);

foreach ($data as $value) {
    $bst->insert($node, $value);
}

// 先序遍历.
echo "\n------------------原始树结构----------------\n";
echo "\n先序搜索\n";
$bst->dfsPreOrder($node);
echo "\n中序搜索\n";
$bst->dfsInOrder($node);
echo "\n后序搜索\n";
$bst->dfsPostOrder($node);


// 删除8.
echo "\n------------------删除叶子结点8----------------\n";
$bst->delete($node, 8);
$bst->dfsPreOrder($node);

// 删除17.
echo "\n------------------删除叶子结点17----------------\n";
$bst->delete($node, 17);
$bst->dfsPreOrder($node);

// 删除15.
echo "\n------------------删除叶子结点15----------------\n";
$bst->delete($node, 15);
$bst->dfsPreOrder($node);