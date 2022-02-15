<?php
/**
 * red black tree node.
 */

namespace DataStructure\ReadBlackTree;


class RedBlackTreeNode
{
    /** @var string $color Color*/
    public $color;
    /** @var mixed $key Value*/
    public $key;
    /** @var RedBlackTreeNode $leftChild Left Node*/
    public $leftChild;
    /** @var RedBlackTreeNode $rightChild Right Node*/
    public $rightChild;
    /** @var RedBlackTreeNode $parent Parent Node*/
    public $parent;

    const COLOR_RED = 'red';
    const COLOR_BLACK = 'black';

    public function __construct($key, $color = self::COLOR_RED, $leftChild = null, $rightChild = null, $parent = null)
    {
        $this->key = $key;
        $this->color = $color;
        $this->leftChild = $leftChild;
        $this->rightChild = $rightChild;
        $this->parent = $parent;
    }
}