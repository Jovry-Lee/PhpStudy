<?php
/**
 * red black tree operation.
 */

namespace DataStructure\ReadBlackTree;

require_once __DIR__ . '/RedBlackTreeNode.php';

class RedBlackTree
{
    /** @var RedBlackTreeNode $nil Sentinel Node.*/
    public $nil;

    /** @var RedBlackTreeNode $root Root Node.*/
    public $root;

    public function __construct()
    {
        $this->nil = new RedBlackTreeNode(null, RedBlackTreeNode::COLOR_BLACK);
        $this->root = $this->nil;
    }

    /**
     * left rotate.
     *
     * @param RedBlackTreeNode $x Node that waiting to rotate.
     *
     * @return void
     */
    public function leftRotate(&$x)
    {
        // temp is x's right child.
        //  $temp = $x->rightChild; 由于PHP的对象是浅拷贝，因此直接采用$temp = $x->rightChild; 赋值的方式导致赋值的为引用，数据逻辑混乱，无法实现功能。
        $temp = new RedBlackTreeNode($x->rightChild->key, $x->rightChild->color, $x->rightChild->leftChild, $x->rightChild->rightChild, $x);

        // turn temp's left subtree into x's right subtree.
        $x->rightChild = $temp->leftChild;
        if ($temp->leftChild !== $this->nil) {
            $temp->leftChild->parent = $x;
        }

        // link x's parent to temp.
        $temp->parent = $x->parent;
        if ($x->parent === $this->nil) {
            $this->root = $temp;
        } elseif ($x === $x->parent->leftChild) {
            $x->parent->leftChild = $temp;
        } else {
            $x->parent->rightChild = $temp;
        }

        // put x on temp's left
        $temp->leftChild = $x;
        $x->parent = $temp;
    }

    /**
     * right rotate.
     *
     * @param RedBlackTreeNode $x Node that waiting to rotate.
     *
     * @return void
     */
    public function rightRotate(&$x)
    {
        // temp is x's left child.
        $temp = new RedBlackTreeNode($x->leftChild->key, $x->leftChild->color, $x->leftChild->leftChild, $x->leftChild->rightChild, $x);
        // turn temp's right subtree into x's left subtree.
        $x->leftChild = $temp->rightChild;
        // set temp's right child's parent to x.
        if ($temp->rightChild !== $this->nil) {
            $temp->rightChild->parent = $x;
        }

        // link x's parent to temp.
        $temp->parent = $x->parent;
        if ($x->parent === $this->nil) {
            $this->root = $temp;
        } elseif ($x->parent->leftChild == $x) {
            $x->parent->leftChild = $temp;
        } else {
            $x->parent->rightChild = $temp;
        }

        // put x on temp's right.
        $temp->rightChild = $x;
        $x->parent = $temp;
    }


    /**
     * Insert a new Node to RB-Tree.
     *
     * @param RedBlackTreeNode $root  current tree root node.
     * @param mixed            $value Value.
     *
     */
    public function insert(&$root, $value)
    {
        $parentNode = $this->nil;
        $currentNode = $root;

        while ($currentNode !== $this->nil) {
            $parentNode = $currentNode;
            if ($value < $currentNode->key) {
                $currentNode = $currentNode->leftChild;
            } else {
                $currentNode = $currentNode->rightChild;
            }
        }

        $newNode = new RedBlackTreeNode($value, RedBlackTreeNode::COLOR_RED, $this->nil, $this->nil, $parentNode);
        if ($parentNode === $this->nil) {
            $this->root = $newNode;
        } elseif ($value < $parentNode->key) {
            $newNode->parent->leftChild = $newNode;
        } else {
            $newNode->parent->rightChild = $newNode;
        }

        $this->insertFixUp($newNode);
    }

    /**
     * Insert Fix Up
     *
     * @param RedBlackTreeNode $root Node that waiting to be fixed up.
     *
     * @return void
     */
    public function insertFixUp(&$root)
    {
        $currentNode = &$root;

        // current node and it's parents's color is red, then continue fix up.
        while ($currentNode->parent->color == RedBlackTreeNode::COLOR_RED) {
            // current node's parent is the left child of it's grandparent.
            $parent = $currentNode->parent;
            $grandParent = $parent->parent;
            if ($parent === $grandParent->leftChild) {
                $uncle = &$grandParent->rightChild;
                // case1 : 当前结点的父结点为red，且当前结点的祖父结点的另一个子节点（叔叔结点）也为red.
                if ($uncle->color == RedBlackTreeNode::COLOR_RED) {
                    // set parent's color to black.
                    $currentNode->parent->color = RedBlackTreeNode::COLOR_BLACK;
                    // set uncle's color to black.
                    $uncle->color = RedBlackTreeNode::COLOR_BLACK;
                    // set grandparent's color to red.
                    $currentNode->parent->parent->color = RedBlackTreeNode::COLOR_RED;
                    // set grandparent to root;
                    $currentNode = &$currentNode->parent->parent;
                } elseif ($currentNode === $currentNode->parent->rightChild) { // Current node is it's parent's right child.
                    // Case 2: 当前结点的父结点是red，叔叔结点为black，当前结点为父结点的右孩子.
                    $currentNode = &$currentNode->parent;
                    $this->leftRotate($currentNode);
                } else {
                    // Case 3: 当前结点的父结点是red，叔叔结点为black，当前结点为父结点的左孩子.
                    $currentNode->parent->color = RedBlackTreeNode::COLOR_BLACK;
                    $currentNode->parent->parent->color = RedBlackTreeNode::COLOR_RED;
                    $this->rightRotate($currentNode->parent->parent);
                }
            } else {
                $uncle = &$grandParent->leftChild;
                if ($uncle->color == RedBlackTreeNode::COLOR_RED) {
                    $currentNode->parent->color = RedBlackTreeNode::COLOR_BLACK;
                    $uncle->color = RedBlackTreeNode::COLOR_BLACK;
                    $currentNode->parent->parent->color = RedBlackTreeNode::COLOR_RED;
                    $currentNode = &$currentNode->parent->parent;
                } elseif ($currentNode === $currentNode->parent->leftChild) {
                    $currentNode = &$currentNode->parent;
                    $this->rightRotate($currentNode);
                } else {
                    $currentNode->parent->color = RedBlackTreeNode::COLOR_BLACK;
                    $currentNode->parent->parent->color = RedBlackTreeNode::COLOR_RED;
                    $this->leftRotate($currentNode->parent->parent);
                }
            }
        }
        $this->root->color = RedBlackTreeNode::COLOR_BLACK;
    }

    public function dfsPreOrder(RedBlackTreeNode $root)
    {
//        var_dump($root->key . "\t" . $root->color);
        echo "$root->key($root->color)-";
        if (!empty($root->leftChild)) {
            $this->dfsPreOrder($root->leftChild);
        }

        if (!empty($root->rightChild)) {
            $this->dfsPreOrder($root->rightChild);
        }
    }

    /**
     * Transplant param v to param u.
     *
     * @param RedBlackTreeNode $u Waiting to be replaced Node.
     * @param RedBlackTreeNode $v Replace Node.
     *
     * @return void
     */
    public function transplant(RedBlackTreeNode &$u, RedBlackTreeNode &$v)
    {
        // $u is Root Node.
        if ($u->parent == $this->nil) {
            $this->root = $v;
        } elseif ($u === $u->parent->leftChild) { // $u is the left child of it's parent.
            $u->parent->leftChild = $v;
        } else {
            $u->parent->rightChild = $v; // $u is the right child of it's parent.
        }
        $v->parent = $u->parent;
    }

    /**
     * Get Minimum Node in the tree that root is $root.
     *
     * @param RedBlackTreeNode $root Node.
     *
     * @return RedBlackTreeNode
     */
    public function minimum($root)
    {
        while ($root->leftChild !== $this->nil) {
            $root = $root->leftChild;
        }
        return $root;
    }

    /**
     * Delete node which node's key equal to $key.
     *
     * @param RedBlackTreeNode $node Node which waiting to be deleted.
     *
     * @return void
     */
    public function delete(&$node)
    {
        $y = $node;
        $yOriginalColor = $y->color;
        if ($node->leftChild === $this->nil) {
            $x = $node->rightChild;
            $this->transplant($node, $node->rightChild);
        } elseif ($node->rightChild === $this->nil) {
            $x = $node->leftChild;
            $this->transplant($node, $node->leftChild);
        } else {
            $y = $this->minimum($node->rightChild);
            $yOriginalColor = $y->color;
            $x = $y->rightChild;
            if ($y->parent == $node) {
                $x->parent = $y;
            } else {
                $this->transplant($y, $y->rightChild);
                $y->rightChild = $node->rightChild;
                $y->rightChild->parent = $y;
            }
            $this->transplant($node, $y);
            $y->leftChild = $node->leftChild;
            $y->leftChild->parent = $y;
            $y->color = $node->color;
        }

        if ($yOriginalColor == RedBlackTreeNode::COLOR_BLACK) {
            $this->deleteFixUp($x);
        }
    }

    /**
     * Delete fix up.
     *
     * @param RedBlackTreeNode $x Node that waiting to be Fixed up.
     *
     * @return void
     */
    public function deleteFixUp(&$x)
    {
        while ($x != $this->root && $x->color == RedBlackTreeNode::COLOR_BLACK) {
            if ($x == $x->parent->leftChild) {
                $w = $x->parent->rightChild;
                // case1：x为“黑+黑”，x的兄弟结点w为红色。
                if ($w->color == RedBlackTreeNode::COLOR_RED) {
                    $w->color = RedBlackTreeNode::COLOR_BLACK;
                    $x->parent->color = RedBlackTreeNode::COLOR_RED;
                    $this->leftRotate($x->parent);
                    $w = $x->parent->rightChild;
                }
                // case2: x为“黑+黑”，x的兄弟结点为黑，x的兄弟结点的两个子结点均为黑色结点.
                if ($w->leftChild->color == RedBlackTreeNode::COLOR_BLACK && $w->rightChild->color == RedBlackTreeNode::COLOR_BLACK) {
                    $w->color = RedBlackTreeNode::COLOR_RED;
                    $x = $x->parent;
                } elseif ($w->rightChild->color == RedBlackTreeNode::COLOR_BLACK) {
                    // case3: x为“黑+黑”，x的兄弟结点为黑，x的兄弟结点w的左子结点为red，w的右子结点为black。
                    $w->leftChild->color = RedBlackTreeNode::COLOR_BLACK;
                    $w->color = RedBlackTreeNode::COLOR_RED;
                    $this->rightRotate($w);
                    $w = $x->parent->rightChild;
                } else {
                    // case4: x为“黑+黑”， x的兄弟结点w为黑，w的右子结点为红。
                    $w->color = $x->parent->color;
                    $x->parent->color = RedBlackTreeNode::COLOR_BLACK;
                    $w->rightChild->color = RedBlackTreeNode::COLOR_BLACK;
                    $this->leftRotate($x->parent);
                    $x = $this->root;
                }
            } else {
                $w = $x->parent->leftChild;
                // case1：x为“黑+黑”，x的兄弟结点w为红色。
                if ($w->color == RedBlackTreeNode::COLOR_RED) {
                    $w->color = RedBlackTreeNode::COLOR_BLACK;
                    $x->parent->color = RedBlackTreeNode::COLOR_RED;
                    $this->rightRotate($x->parent);
                }
                // case2: x为“黑+黑”，x的兄弟结点为黑，x的兄弟结点的两个子结点均为黑色结点.
                if ($w->leftChild->color == RedBlackTreeNode::COLOR_BLACK && $w->rightChild->color == RedBlackTreeNode::COLOR_BLACK) {
                    $w->color = RedBlackTreeNode::COLOR_RED;
                    $x = $x->parent;
                } elseif ($w->leftChild->color == RedBlackTreeNode::COLOR_BLACK) {
                    // case3: x为“黑+黑”，x的兄弟结点为黑，x的兄弟结点w的右子结点为red，w的左子结点为black。
                    $w->rightChild->color = RedBlackTreeNode::COLOR_BLACK;
                    $w->color = RedBlackTreeNode::COLOR_RED;
                    $this->leftRotate($w);
                    $w = $x->parent->leftChild;
                } else {
                    // case4: x为“黑+黑”， x的兄弟结点w为黑，w的左子结点为红。
                    $w->color = $x->parent->color;
                    $x->parent->color = RedBlackTreeNode::COLOR_BLACK;
                    $w->leftChild->color = RedBlackTreeNode::COLOR_BLACK;
                    $this->rightRotate($x->parent);
                    $x = $this->root;
                }
            }
        }
        $x->color = RedBlackTreeNode::COLOR_BLACK;
    }


    /**
     * Search node that node's key equal to $key.
     *
     * @param RedBlackTreeNode $root Current node.
     * @param mixed $key Key.
     *
     * @return RedBlackTreeNode
     */
    public function search(&$root, $key)
    {
        if ($root == $this->nil || $root->key == $key) {
            return $root;
        }

        if ($key < $root->key) {
            return $this->search($root->leftChild, $key);
        }
        return $this->search($root->rightChild, $key);
    }
}


/********************************************************* Delete Test Begin *********************************************************/
/*
    

*/




/********************************************************* Delete Test End ***********************************************************/


