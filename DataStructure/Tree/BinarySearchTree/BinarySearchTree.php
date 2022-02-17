<?php
/**
 * 二叉搜索树。
 */
namespace DataStructure\Tree\BinarySearchTree;

use DataStructure\Tree\BinaryTree\BinaryTree;
use DataStructure\Tree\BinaryTree\Node;

class BinarySearchTree extends BinaryTree
{
    /**
     * Insert child.
     *
     * @param Node  &$root 待插入的结点.
     * @param mixed $x     待插入的值.
     *
     * @return void
     */
    public function insert(&$root, $x)
    {
        if (empty($root)) {
            $root = new Node($x);
            return ;
        }

        if ($x == $root->value) { // 查找成功, 说明结点已存在.
            return ;
        } elseif ($x < $root->value) { // 插入值小于当前结点值, 插入左子树.
            $this->insert($root->leftChild, $x);
        } else { // 插入值大于当前结点值, 插入左子树.
            $this->insert($root->rightChild, $x);
        }
    }

    /**
     * Search:查找二叉树中数据为x的结点.
     *
     * @param Node  &$root 根结点.
     * @param mixed $x     查找的值.
     *
     * @return boolean
     */
    public function search($root, $x)
    {
        if (empty($root)) {
            var_dump("search failed");
            return ;
        }

        $currentValue = $root->getValue();
        if ($x == $currentValue) {
            var_dump($root->value);
        } elseif ($x < $currentValue) {
            $this->search($root->getLeftChild(), $x);
        } else {
            $this->search($root->getRightChild(), $x);
        }
    }

    /**
     * FindMax:寻找以root为根结点的树中权值最大的结点.
     *
     * @param Node  &$root 根结点.
     *
     * @return Node
     */
    public function findMax($root)
    {
        while (!empty($root->rightChild)) {
            $root = $root->rightChild;
        }
        return $root;
    }

    /**
     * FindMax:寻找以root为根结点的树中权值最小的结点.
     *
     * @param Node  &$root 根结点.
     *
     * @return Node
     */
    public function findMin($root)
    {
        while (!empty($root->leftChild)) {
            $root = $root->leftChild;
        }
        return $root;
    }


    /**
     * Delete:删除以root为根结点的树中权值为x的结点.
     *
     * @param Node  &$root 根结点.
     * @param mixed $x     查找的值.
     *
     * @return void
     */
    public function delete(&$root, $x)
    {
        if (empty($root)) { // 不存在权值为x的结点.
            return ;
        }

        if ($root->value == $x) {
            if (empty($root->leftChild) && empty($root->rightChild)) {
                $root = null;
            } elseif (!empty($root->leftChild)) {
                $pre = $this->findMax($root->leftChild);
                $root->value = $pre->value;
                $this->delete($root->leftChild, $pre->value);
            } else {
                $next = $this->findMin($root->rightChild);
                $root->value = $next->value;
                $this->delete($root->rightChild, $next->value);
            }
        } elseif ($root->value > $x) {
            $this->delete($root->leftChild, $x);
        } else {
            $this->delete($root->rightChild, $x);
        }
    }
}

















