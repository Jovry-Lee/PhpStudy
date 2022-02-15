<?php

namespace DataStructure\ReadBlackTree;

require_once __DIR__ . '/../RedBlackTree.php';
require_once __DIR__ . '/../RedBlackTreeNode.php';

/********************************************************* Delete Test Begin *********************************************************/
/** Case3: Deleted node's left child and right child is not nil, and it's successor is black, successor's brother is red.
    This situation include case 1, case2.
 */
#                       |80|                                                                          |90|
#                      /    \                                                                         /   \
#                     /      \                                                                       /     \
#                    /        \                                                                     /       \
#                   /          \                                                                   /         \
#                  /            \                                                                 /           \
#                 /              \                                                               /             \
#                /                \                                                             /               \
#               /                  \                            (Delete 80)                    /                 \
#            |40|                  |110|                       ------------->               |40|                  |150|
#             /\                   /   \                                                     /\                    / \
#            /  \                 /     \                                                   /  \                  /   \
#           /    \               /       \                                                 /    \                /     \
#          /      \             /         \                                               /      \              /       \
#         /        \           /           \                                             /        \            /         \
#       |20|      |60|     |90|             150                                        |20|      |60|       |110|      |170|
#       /   \    /   \        \             /  \                                        / \       / \        /  \       /  \
#      /     \  /     \        \           /    \                                      /   \     /   \      /    \     /    \
#    |10| |30| |50| |70|      |100|    |130|    |170|                                |10| |30| |50| |70| |100|  130  |160| |180|
#                                      /   \    /   \                                                           /  \
#                                  |120| |140| |160| |180|                                                   |120| |140|

$rbTree1 = new RedBlackTree();
$nil = $rbTree1->nil;

$node80 = new RedBlackTreeNode(80, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $nil);
$node40 = new RedBlackTreeNode(40, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node80);
$node20 = new RedBlackTreeNode(20, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node40);
$node10 = new RedBlackTreeNode(10, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node20);
$node30 = new RedBlackTreeNode(30, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node20);
$node60 = new RedBlackTreeNode(60, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node40);
$node50 = new RedBlackTreeNode(50, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node60);
$node70 = new RedBlackTreeNode(70, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node60);
$node110 = new RedBlackTreeNode(110, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node80);
$node90 = new RedBlackTreeNode(90, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node110);
$node100 = new RedBlackTreeNode(100, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node90);
$node150 = new RedBlackTreeNode(150, RedBlackTreeNode::COLOR_RED, $nil, $nil, $node110);
$node130 = new RedBlackTreeNode(130, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node150);
$node120 = new RedBlackTreeNode(120, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node130);
$node140 = new RedBlackTreeNode(140, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node130);
$node170 = new RedBlackTreeNode(170, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node150);
$node160 = new RedBlackTreeNode(160, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node170);
$node180 = new RedBlackTreeNode(180, RedBlackTreeNode::COLOR_BLACK, $nil, $nil, $node170);

$node80->leftChild = $node40;
$node40->leftChild = $node20;
$node20->leftChild = $node10;
$node20->rightChild = $node30;
$node40->rightChild = $node60;
$node60->leftChild = $node50;
$node60->rightChild = $node70;

$node80->rightChild = $node110;
$node110->leftChild = $node90;
$node90->rightChild = $node100;
$node110->rightChild = $node150;
$node150->leftChild = $node130;
$node130->leftChild = $node120;
$node130->rightChild = $node140;
$node150->rightChild = $node170;
$node170->leftChild = $node160;
$node170->rightChild = $node180;



$rbTree1->root = $node80;

echo "Original RB-Tree DFS: ";
$rbTree1->dfsPreOrder($rbTree1->root);
echo "\n";
echo "\n";


$deleteNodeKey = 80;
$deleteNode = $rbTree1->search($rbTree1->root, $deleteNodeKey);
echo "Delete node info: key = {$deleteNode->key}, color is {$deleteNode->color}\n\n";


$rbTree1->delete($deleteNode);

echo "After Delete {$deleteNodeKey} RB-Tree DFS: ";
$rbTree1->dfsPreOrder($rbTree1->root);
echo "\n";
die;
/********************************************************* Delete Test End *********************************************************/


// Execute Result:

// Original RB-Tree DFS: 80(black)-40(black)-20(black)-10(black)-(black)-(black)-30(black)-(black)-(black)-60(black)-50(black)-(black)-(black)-70(black)-(black)-(black)-110(black)-90(black)-(black)-100(black)-(black)-(black)-150(red)-130(black)-120(black)-(black)-(black)-140(black)-(black)-(black)-170(black)-160(black)-(black)-(black)-180(black)-(black)-(black)-

// Delete node info: key = 80, color is black

// After Delete 80 RB-Tree DFS: 90(black)-40(black)-20(black)-10(black)-(black)-(black)-30(black)-(black)-(black)-60(black)-50(black)-(black)-(black)-70(black)-(black)-(black)-150(black)-110(black)-100(black)-(black)-(black)-130(red)-120(black)-(black)-(black)-140(black)-(black)-(black)-170(black)-160(black)-(black)-(black)-180(black)-(black)-(black)-
