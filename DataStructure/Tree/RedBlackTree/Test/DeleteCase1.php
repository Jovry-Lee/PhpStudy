<?php

namespace DataStructure\Tree\RedBlackTree;

require_once __DIR__ . "/../../../../init.php";

/********************************************************* Delete Test Begin *********************************************************/
/** Case1: Delete node's left child is nil(Include two situation: Delete node's right node is nil or not nil) */
#        |50|                               |50|
#      /      \                           /      \
#    |30|     |70|   (DELETE 30) --->   |40|     |70|
#        \
#        40
$rbTree1 = new RedBlackTree();
$nil = $rbTree1->nil;
$node50 = new RedBlackTreeNode(50, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $nil);
$node30 = new RedBlackTreeNode(30, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node50);
$node70 = new RedBlackTreeNode(70, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node50);
$node40 = new RedBlackTreeNode(40, RedBlackTreeNode::COLOR_RED, $nil, $nil, $node30);

$node50->leftChild = $node30;
$node50->rightChild = $node70;
$node30->rightChild = $node40;
$rbTree1->root = $node50;

echo "Original RB-Tree: ";
$rbTree1->dfsPreOrder($rbTree1->root);
echo "\n";

$deleteNode = $rbTree1->search($rbTree1->root, 30);
$rbTree1->delete($deleteNode);

echo "After Delete RB-Tree: ";
$rbTree1->dfsPreOrder($rbTree1->root);
echo "\n";
die;
/********************************************************* Delete Test End *********************************************************/
// Execute Result:

// Original RB-Tree: 50(black)-30(black)-(black)-40(red)-(black)-(black)-70(black)-(black)-(black)
// After Delete RB-Tree: 50(black)-40(black)-(black)-(black)-70(black)-(black)-(black)