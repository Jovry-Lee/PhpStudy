<?php
/**
 * 二叉树。
 */
namespace DataStructure\Tree\BinaryTree;

class BinaryTree
{
    /**
     * Insert left child.
     *
     * @param Node  &$root 待插入的左结点.
     * @param mixed $x     待插入的值.
     *
     * @return void
     */
    public function insertLeft(&$root, $x)
    {
        if (empty($root)) {
            $root = new Node($x);
            return ;
        }

        $this->insertLeft($root->leftChild, $x);
    }

    /**
     * Insert right child.
     *
     * @param Node  &$root 待插入的右结点.
     * @param mixed $x     待插入的值.
     *
     * @return void
     */
    public function insertRight(&$root, $x)
    {
        if (empty($root)) {
            $root = new Node($x);
            return ;
        }

        $this->insertRight($root->rightChild, $x);
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
            return;
        }

        $currentValue = $root->value;
        if ($x == $currentValue) {
            return true;
        }

        if ($this->search($root->leftChild, $x) === true || $this->search($root->rightChild, $x) === true) {
            return true;
        }
        return false;
    }

    /**
     * DFS-PreOrder 深度优先搜索算法-前序.
     *
     * @param Node $root 二叉数根结点.
     *
     * @return void
     */
    public function dfsPreOrder($root)
    {
        var_dump($root->value);
        if (!empty($root->getLeftChild())) {
            $this->dfsPreOrder($root->leftChild);
        }

        if (!empty($root->getRightChild())) {
            $this->dfsPreOrder($root->rightChild);
        }
    }

    /**
     * DFS-InOrder 深度优先搜索算法-中序.
     *
     * @param Node $root 二叉数根结点.
     *
     * @return void
     */
    public function dfsInOrder($root)
    {
        if (!empty($root->getLeftChild())) {
            $this->dfsInOrder($root->leftChild);
        }
        var_dump($root->value);

        if (!empty($root->getRightChild())) {
            $this->dfsInOrder($root->rightChild);
        }
    }

    /**
     * DFS-InOrder 深度优先搜索算法-后序.
     *
     * @param Node $root 二叉数根结点.
     *
     * @return void
     */
    public function dfsPostOrder($root)
    {
        if (!empty($root->getLeftChild())) {
            $this->dfsPostOrder($root->leftChild);
        }

        if (!empty($root->getRightChild())) {
            $this->dfsPostOrder($root->rightChild);
        }
        var_dump($root->value);
    }
}
