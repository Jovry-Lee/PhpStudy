<?php

namespace DataStructure\ReadBlackTree;

require_once __DIR__ . '/../RedBlackTree.php';
require_once __DIR__ . '/../RedBlackTreeNode.php';

/********************************************************* Insert Test Begin *********************************************************/
/** Case1: Insert node's parent is the left child of it's grandparent. */
$testLeft = array(80, 40, 120, 20, 60, 50, 70, 140, 45);

$rbTree1 = new RedBlackTree();
foreach ($testLeft as $value) {
    $rbTree1->insert($rbTree1->root, $value);
}

echo "Left side RB-Tree: \n";
$rbTree1->dfsPreOrder($rbTree1->root);
echo "\n";
echo "\n";

/** Case2: Insert node's parent is the right child of it's grandparent. */
$testRight = array(80, 120, 40, 140, 100, 90, 110, 20, 115);

$rbTree2 = new RedBlackTree();
foreach ($testRight as $value) {
    $rbTree2->insert($rbTree2->root, $value);
}
echo "Right side RB-Tree: \n";
$rbTree2->dfsPreOrder($rbTree2->root);
echo "\n";

/********************************************************* Insert Test End *********************************************************/