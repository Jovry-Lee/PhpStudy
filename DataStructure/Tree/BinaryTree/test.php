<?php

require_once __DIR__ . "/../../../init.php";

use \DataStructure\Tree\BinaryTree\BinaryTree;

$bt = new BinaryTree();
$rootNode = null;

$bt->insertRight($rootNode, 'a');
$bt->insertLeft($rootNode, 'b');
$bt->insertRight($rootNode, 'c');

$bt->insertLeft($rootNode->leftChild, 'd');
$bt->insertLeft($rootNode->rightChild, 'e');
$bt->insertRight($rootNode->rightChild, 'f');

echo "\n深度优先-先序遍历\n";
$bt->dfsPreOrder($rootNode);
echo "\n深度优先-中序遍历\n";
$bt->dfsInOrder($rootNode);
echo "\n深度优先-后序遍历\n";
$bt->dfsPostOrder($rootNode);