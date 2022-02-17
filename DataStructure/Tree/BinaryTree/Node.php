<?php

namespace DataStructure\Tree\BinaryTree;

/**
 * 树结点。
 */
class Node
{
    /** @var mixed $value*/
    public $value;
    /** @var Node $leftChild*/
    public $leftChild;
    /** @var Node $rightChild*/
    public $rightChild;

    /**
     * Set value.
     *
     * @param mixed $value Node Value.
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
     * @param Node $leftChild Left Child.
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
     * @param Node $rightChild Right Child.
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get leftChild.
     *
     * @return Node
     */
    public function getLeftChild()
    {
        return $this->leftChild;
    }

    /**
     * Get right child.
     *
     * @return Node
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
}