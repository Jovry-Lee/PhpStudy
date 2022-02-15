<?php

class AVLTree
{
    /** @var Integer $value 节点权值*/
    public $value;
    /** @var Integer $height 当前子树高度*/
    public $height;
    /** @var AVLTree $leftChild 左子结点*/
    public $leftChild;
    /** @var AVLTree $rightChild 右字结点*/
    public $rightChild;

    /**
     * 构造函数.
     *
     * @param integer $value 结点权值.
     */
    public function __construct($value)
    {
        $this->value = $value;
        $this->height = 1;
        $this->leftChild = null;
        $this->rightChild = null;
    }

    /**
     * 获取子树的当前高度.
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * 获取结点平衡因子.
     *
     * @return integer
     */
    public function getBalanceFactor()
    {
        // 左子树高度减右子树高度.
        $leftChildHeight = empty($this->leftChild) ? 0 : $this->leftChild->getHeight();
        $rightChildHeight = empty($this->rightChild) ? 0 : $this->rightChild->getHeight();

        return $leftChildHeight - $rightChildHeight;
    }

    /**
     * 更新结点的height.
     *
     * @return void
     */
    public function updateHeight()
    { 
        // max(左子结点的height, 右子结点的height) + 1.
        $leftChildHeight = empty($this->leftChild) ? 0 : $this->leftChild->getHeight();
        $rightChildHeight = empty($this->rightChild) ? 0 : $this->rightChild->getHeight();

        $this->height = max($leftChildHeight, $rightChildHeight) + 1;
    }

    /**
     * 查找AVL树中值为value的结点.
     *
     * @param String $value Node Value.
     *
     * @return boolean
     */
    public function search($value)
    {
        $currenNodeValue = $this->value;
        if ($value < $currenNodeValue && !empty($this->leftChild)) {
            return $this->leftChild->search($value);
        }
        if ($value > $currenNodeValue && !empty($this->rightChild)) {
            return $this->rightChild->search($value);
        }
        return $value == $currenNodeValue;
    }

    /**
     * 以当前结点向左旋转.
     */
    public function leftRotation()
    {
        $root = &$this;

        $tmpNode = $this->rightChild;
        $this->rightChild = $tmpNode->leftChild;
        $tmpNode->leftChild = $this;

        $this->updateHeight();
        $tmpNode->updateHeight();
        $root = $tmpNode;
    }

    /**
     * 以当前结点向右旋转.
     */
    public function rightRotation()
    {
        $root = &$this;

        $tmpNode = $this->leftChild;
        $this->leftChild = $tmpNode->rightChild;
        $tmpNode->rightChild = $this;
        $this->updateHeight();
        $tmpNode->updateHeight();
        $root = $tmpNode;
    }

    /**
     * 插入权值为value的结点.
     *
     * @param integer $value 权值.
     *
     * @return void
     */
    public function insertNode($value)
    {
        $currentNodeValue = $this->value;

        if ($value <= $currentNodeValue && !empty($this->leftChild)) {
            $this->leftChild->insertNode($value);

            //判断树型.
            if ($this->getBalanceFactor() == 2) {
                if ($this->leftChild->getBalanceFactor() == 1) { // LL型.
                    var_dump('LL');
                    $this->rightRotation();
                } elseif ($this->leftChild->getBalanceFactor() == -1) { // LR型.
                    var_dump('LR');
                    $this->leftChild->leftRotation();
                    $this->rightRotation();
                }
            }
        } elseif ($value <= $currentNodeValue) {
            $this->leftChild = new AVLTree($value);
        } elseif ($value > $currentNodeValue && !empty($this->rightChild)) {
            $this->rightChild->insertNode($value);
            // 更新树高.
            $this->updateHeight();

            //判断树型.
            if ($this->getBalanceFactor() == -2) {
                if ($this->rightChild->getBalanceFactor() == -1) { // RR型.
                    var_dump('RR');
                    $this->leftRotation();
                } elseif ($this->rightChild->getBalanceFactor() == 1) { // RL型.
                    var_dump('RL');
                    $this->rightChild->rightRotation();
                    $this->leftRotation();
                }
            }
        } else {
            $this->rightChild = new AVLTree($value);
            // 更新树高.
            $this->updateHeight();
        }
    }


//    /**
//     * 插入权值为value的结点.
//     *
//     * @param integer $value 权值.
//     *
//     * @return void
//     */
//    public function insertNode($value)
//    {
//        $currentNodeValue = $this->value;
//        if ($value <= $currentNodeValue && !empty($this->leftChild)) {
//            $this->leftChild->insertNode($value);
//        } elseif ($value <= $currentNodeValue) {
//            $this->leftChild = new BinarySearchTree($value);
//        } elseif ($value > $currentNodeValue && !empty($this->rightChild)) {
//            $this->rightChild->insertNode($value);
//        } else {
//            $this->rightChild = new BinarySearchTree($value);
//        }
//
//
//
//
//        $currentNodeValue = $this->value;
//        if ($value <= $currentNodeValue) {
//            if (!empty($this->leftChild)) {
//                $this->leftChild->insertNode($value);
//            }
//            $this->leftChild = new AVLTree($value);
//            // 更新树高.
//            $this->updateHeight();
//
//            //判断树型.
//            if ($this->getBalanceFactor() == 2) {
//                if ($this->leftChild->getBalanceFactor() == 1) { // LL型.
//                    var_dump('LL');
//                    $this->rightRotation();
//                } elseif ($this->leftChild->getBalanceFactor() == -1) { // LR型.
//                    var_dump('LR');
//                    $this->leftChild->leftRotation();
//                    $this->rightRotation();
//                }
//            }
//        } else {
//            if (!empty($this->rightChild)) {
//                $this->rightChild->insertNode($value);
//            }
//            $this->rightChild = new AVLTree($value);
//            // 更新树高.
//            $this->updateHeight();
//
//            //判断树型.
//            if ($this->getBalanceFactor() == -2) {
//                if ($this->rightChild->getBalanceFactor() == -1) { // RR型.
//                    var_dump('RR');
//                    $this->leftRotation();
//                } elseif ($this->rightChild->getBalanceFactor() == 1) { // RL型.
//                    var_dump('RL');
//                    $this->rightChild->rightRotation();
//                    $this->leftRotation();
//                }
//            }
//        }
//    }

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

$aVLTree = new AVLTree(0);
var_dump("插入0后树的高度为:" . $aVLTree->getHeight());
for ($i = 1; $i < 4; $i++) {
    $aVLTree->insertNode($i);
    var_dump("插入{$i}后树的高度为:" . $aVLTree->getHeight());
}

$aVLTree->dfsPreOrder();
