<?php
class BinarySearchTree
{
    /** @var String $value*/
    private $value;
    /** @var BinarySearchTree $leftChild*/
    private $leftChild;
    /** @var BinarySearchTree $rightChild*/
    private $rightChild;

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
     * @param BinarySearchTree $leftChild Left Child.
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
     * @param BinarySearchTree $rightChild Right Child.
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
     * @return BinarySearchTree
     */
    public function getLeftChild()
    {
        return $this->leftChild;
    }

    /**
     * Get right child.
     *
     * @return BinarySearchTree
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
     * Insert Node.
     *
     * @param String $value Node Value.
     *
     * @return void
     */
    public function insertNode($value)
    {
        $currentNodeValue = $this->getValue();
        if ($value <= $currentNodeValue && !empty($this->leftChild)) {
            $this->leftChild->insertNode($value);
        } elseif ($value <= $currentNodeValue) {
            $this->leftChild = new BinarySearchTree($value);
        } elseif ($value > $currentNodeValue && !empty($this->rightChild)) {
            $this->rightChild->insertNode($value);
        } else {
            $this->rightChild = new BinarySearchTree($value);
        }
    }

    /**
     * Find Node.
     *
     * @param String $value Node Value.
     *
     * @return boolean
     */
    public function findNode($value)
    {
        $currenNodeValue = $this->getValue();
        if ($value < $currenNodeValue && !empty($this->leftChild)) {
            return $this->leftChild->findNode($value);
        }
        if ($value > $currenNodeValue && !empty($this->rightChild)) {
            return $this->rightChild->findNode($value);
        }
        return $value == $currenNodeValue;
    }

    /**
     * DFS-PreOrder 深度优先搜索算法-前序.
     *
     * @return void
     */
    public function dfsPreOrder()
    {
        var_dump($this->value);
        if ($leftChild = $this->leftChild) {
            $leftChild->dfsPreOrder();
        }

        if ($rightChild = $this->rightChild) {
            $rightChild->dfsPreOrder();
        }
    }

}


$bst = new BinarySearchTree(0);

for ($i = 1; $i < 10; $i++) {
    $bst->insertNode($i);
}

$bst->dfsPreOrder();