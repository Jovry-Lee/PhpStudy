<?php

namespace DataStructure;

/**
 * AVL树结点。
 */
class AVLTreeNode
{
    /** @var Integer $value*/
    public $value;
    /** @var Integer $height*/
    public $height;
    /** @var AVLTreeNode $leftChild*/
    public $leftChild;
    /** @var AVLTreeNode $rightChild*/
    public $rightChild;

    /**
     * Set value.
     *
     * @param integer $value Node Value.
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
     * @param AVLTreeNode $leftChild Left Child.
     *
     * @return void
     */
    public function setLeftChild($leftChild)
    {
        $this->leftChild = $leftChild;
    }

    /**
     * Set height.
     *
     * @param Integer $height 树高.
     *
     * @return void
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Set right child.
     *
     * @param AVLTreeNode $rightChild Right Child.
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
     * @return Integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get leftChild.
     *
     * @return AVLTreeNode
     */
    public function getLeftChild()
    {
        return $this->leftChild;
    }

    /**
     * Get right child.
     *
     * @return AVLTreeNode
     */
    public function getRightChild()
    {
        return $this->rightChild;
    }

    /**
     * Get height.
     *
     * @return Integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * AVLTreeNode构造器.
     */
    public function __construct($value)
    {
        $this->value = $value;
        $this->height = 1;
        $this->leftChild = null;
        $this->rightChild = null;
    }
}