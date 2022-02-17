<?php

use DataStructure\Tree\AVLTree\AVLTree;

require_once __DIR__ . "/../../../init.php";

$avl = new AVLTree();
$node = null;
var_dump("树的高度为:" . $avl->getHeight($node));
for ($i =1; $i<4; $i++) {
    $avl->insertNode($node, $i);
    var_dump("插入{$i}后树的高度为:" . $avl->getHeight($node));
}

$avl->dfsPreOrder($node);