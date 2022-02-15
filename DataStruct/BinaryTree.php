<?php

/**
 * 二叉数.
 */
class BinaryTree
{
    /** @var String $value*/
    protected $value;
    /** @var BinaryTree $leftChild*/
    protected $leftChild;
    /** @var BinaryTree $rightChild*/
    protected $rightChild;

    /**
     * Set value.
     *
     * @param String $value Node Value.
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Set leftChild.
     *
     * @param BinaryTree $leftChild Left Child.
     *
     * @return void
     */
    public function setLeftChild($leftChild)
    {
        $this->leftChild = $leftChild;
    }

    /**
     * Set right child.
     *
     * @param BinaryTree $rightChild Right Child.
     *
     * @return void
     */
    public function setRightChild($rightChild)
    {
        $this->rightChild = $rightChild;
    }

    /**
     * Get value.
     *
     * @return String
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get leftChild.
     *
     * @return BinaryTree
     */
    public function getLeftChild()
    {
        return $this->leftChild;
    }

    /**
     * Get right child.
     *
     * @return BinaryTree
     */
    public function getRightChild()
    {
        return $this->rightChild;
    }

    public function __construct($value)
    {
        $this->value = $value;
        $this->leftChild = null;
        $this->rightChild = null;
    }

    /**
     * Insert left child.
     *
     * @param String $value Node Value.
     *
     * @return void
     */
    public function insertLeft($value)
    {
        // 若当前无左字节点, 则新建一个节点,将其分配于字节点.
        $leftChild = $this->getLeftChild();
        if (empty($leftChild)) {
            $this->setLeftChild(new BinaryTree($value));
        } else {
            // 若当前存在左字节点, 则创建一个新的节点,并把当前左子节点分配于新建节点的左字节点上, 然后将新建的节点分配于当前节点的左字节点.
            $newNode = new BinaryTree($value);
            $newNode->setLeftChild($leftChild);
            $this->setLeftChild($newNode);
        }
    }

    /**
     * Insert right child.
     *
     * @param String $value Node Value.
     *
     * @return void
     */
    public function insertRight($value)
    {
        // 若当前无右字节点, 则新建一个节点,将其分配于字节点.
        $rightChild = $this->getRightChild();
        if (empty($rightChild)) {
            $this->setRightChild(new BinaryTree($value));
        } else {
            // 若当前存在右字节点, 则创建一个新的节点,并把当前左子节点分配于新建节点的右字节点上, 然后将新建的节点分配于当前节点的右字节点.
            $newNode = new BinaryTree($value);
            $newNode->setRightChild($rightChild);
            $this->setRightChild($newNode);
        }
    }

    /**
     * DFS-PreOrder 深度优先搜索算法-前序.
     *
     * @return void
     */
    public function dfsPreOrder()
    {
        var_dump($this->getValue());
        if ($leftChild = $this->getLeftChild()) {
            $leftChild->dfsPreOrder();
        }

        if ($rightChild = $this->getRightChild()) {
            $rightChild->dfsPreOrder();
        }
    }

    /**
     * DFS-InOrder 深度优先搜索算法-中序.
     *
     * @return void
     */
    public function dfsInOrder()
    {
        if ($leftChild = $this->getLeftChild()) {
            $leftChild->dfsInOrder();
        }
        var_dump($this->getValue());

        if ($rightChild = $this->getRightChild()) {
            $rightChild->dfsInOrder();
        }
    }

    /**
     * DFS-InOrder 深度优先搜索算法-后序.
     *
     * @return void
     */
    public function dfsPostOrder()
    {
        if ($leftChild = $this->getLeftChild()) {
            $leftChild->dfsPostOrder();
        }

        if ($rightChild = $this->getRightChild()) {
            $rightChild->dfsPostOrder();
        }

        var_dump($this->getValue());
    }

    /**
     * BFS 深度优先搜索算法.
     *
     * @return void
     */
    public function bfs()
    {

    }
}

/**
// test one.
$binaryTree = new BinaryTree('a');
var_dump(
    $binaryTree->getValue(),
    $binaryTree->getLeftChild(),
    $binaryTree->getRightChild()
);
 */

// test two.
$aNode = new BinaryTree('a');
$aNode->insertLeft('b');
$aNode->insertRight('c');

$bNode = $aNode->getLeftChild();
$bNode->insertRight('d');

$cNode = $aNode->getRightChild();
$cNode->insertLeft('e');
$cNode->insertRight('f');

$dNode = $bNode->getRightChild();
$eNode = $cNode->getLeftChild();
$fNode = $cNode->getRightChild();

var_dump(
    $aNode->getValue(),
    $bNode->getValue(),
    $cNode->getValue(),
    $dNode->getValue(),
    $eNode->getValue(),
    $fNode->getValue()
);


