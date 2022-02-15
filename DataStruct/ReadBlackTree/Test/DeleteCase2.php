<?php

namespace DataStructure\ReadBlackTree;

require_once __DIR__ . '/../RedBlackTree.php';
require_once __DIR__ . '/../RedBlackTreeNode.php';

/********************************************************* Delete Test Begin *********************************************************/
/** Case2: Delete node's right child is nil(Include two situation: Delete node's right node is nil or not nil) */
#        |50|                               |50|
#       /      \                           /      \
#     |30|     |70|   (DELETE 30) --->   |20|     |70|
#    /
#   20
$rbTree1 = new RedBlackTree();
$nil = $rbTree1->nil;
$node50 = new RedBlackTreeNode(50, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $nil);
$node30 = new RedBlackTreeNode(30, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node50);
$node70 = new RedBlackTreeNode(70, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node50);
$node20 = new RedBlackTreeNode(20, RedBlackTreeNode::COLOR_RED, $nil, $nil, $node30);

$node50->leftChild = $node30;
$node50->rightChild = $node70;
$node30->leftChild = $node20;
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

// Original RB-Tree: 50(black)-30(black)-20(red)-(black)-(black)-(black)-70(black)-(black)-(black)
// After Delete RB-Tree: 50(black)-20(black)-(black)-(black)-70(black)-(black)-(black)
