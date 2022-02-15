<?php

include_once 'AVLTreeNode.php';

/**
 * 平衡二叉树.
 */
class AVLTree
{
    /**
     * GetHeight: 获取以root为根结点的子树的当前height.
     *
     * @param \DataStructure\AVLTreeNode $root 根结点.
     *
     * @return integer
     */
    public function getHeight($root)
    {
        if (empty($root)) {
            return 0;
        }
        return $root->getHeight();
    }

    /**
     * GetBalanceFactor: 计算结点root的平衡因子.
     *
     * @param \DataStructure\AVLTreeNode $root 根结点.
     *
     * @return integer
     */
    public function getBalanceFactor($root)
    {
        // 左子树高度-右子树高度.
        return $this->getHeight($root->leftChild) - $this->getHeight($root->rightChild);
    }

    /**
     * UpdateHeight: 更新结点root的height.
     *
     * @param \DataStructure\AVLTreeNode $root 根结点.
     *
     * @return void
     */
    public function updateHeight(&$root)
    {
        if (empty($root)) {
            return ;
        }
        // max(左孩的height, 右孩的height)+1.
        $root->height = max($this->getHeight($root->leftChild), $this->getHeight($root->rightChild)) + 1;
    }

    public function search($root, $x)
    {
        if (empty($root)) {
            return false;
        }

        $currentValue = $root->getValue();
        if ($x == $currentValue) {
            return true;
        } elseif ($x < $currentValue) {
            $this->search($root->getLeftChild(), $x);
        } else {
            $this->search($root->getRightChild(), $x);
        }
    }

    /**
     * RightRought: 对root结点进行右旋操作.
     *
     * @param \DataStructure\AVLTreeNode &$root 根结点.
     *
     * @return void
     */
    public function rightRought(&$root)
    {
        // temp为root结点的左孩子.
        $temp = $root->leftChild;
        // 将temp的右孩子成为root的左孩子.
        $root->leftChild = $temp->rightChild;
        // 将root结点成为temp的右孩子.
        $temp->rightChild = $root;
        // 更新root结点的高度.
        $this->updateHeight($root);
        // 更新temp结点的高度.
        $this->updateHeight($temp);
        // 将根结点设置为A.
        $root = $temp;
    }

    /**
     * LeftRought: 对root结点进行左旋操作.
     *
     * @param \DataStructure\AVLTreeNode &$root 根结点.
     *
     * @return void
     */
    public function leftRought(&$root)
    {
        // temp为root结点的右孩.
        $temp = $root->rightChild;
        // 将temp的左孩子成为root结点的右孩子.
        $root->rightChild = $temp->leftChild;
        // 将root结点称为temp结点左孩子.
        $temp->leftChild = $root;
        // 更新root结点的高度.
        $this->updateHeight($root);
        // 更新temp结点的高度.
        $this->updateHeight($temp);
        // 将根结点设置为B.
        $root = $temp;
    }

    /**
     * InsertNode: 插入权值为value的结点.
     *
     * @param \DataStructure\AVLTreeNode &$root 根结点.
     * @param mixed                      $value 权值.
     *
     * @return void
     */
    public function insertNode(&$root, $value)
    {
        // 到达空结点.
        if (empty($root)) {
            $root = new \DataStructure\AVLTreeNode($value);
            return;
        }

        // value权值小于root结点权值.
        $currentNodeValue = $root->getValue();
        if ($value < $currentNodeValue) {
            // 往左子树插入.
            $this->insertNode($root->leftChild, $value);
            // 更新树高.
            $this->updateHeight($root);
            if ($this->getBalanceFactor($root) == 2) {
                $leftChildBalanceFactor = $this->getBalanceFactor($root->leftChild);
                if ($leftChildBalanceFactor == 1) { // LL型.
                    $this->rightRought($root);
                } elseif ($leftChildBalanceFactor == -1) { // LR型.
                    $this->leftRought($root->leftChild);
                    $this->rightRought($root);
                }
            }
        } else { // value的权值大于root结点权值.
            // 往右子树插入.
            $this->insertNode($root->rightChild, $value);
            // 更新树高.
            $this->updateHeight($root);
            if ($this->getBalanceFactor($root) == -2) {
                $rightChildBalanceFactor = $this->getBalanceFactor($root->rightChild);
                if ($rightChildBalanceFactor == -1) { // RR型.
                    $this->leftRought($root);
                } elseif ($rightChildBalanceFactor == 1) { // RL型.
                    $this->rightRought($root->rightChild);
                    $this->leftRought($root);
                }
            }
        }

    }

    public function dfsPreOrder($root)
    {
        var_dump($root->value);
        if (!empty($root->getLeftChild())) {
            $this->dfsPreOrder($root->getLeftChild());
        }

        if (!empty($root->getRightChild())) {
            $this->dfsPreOrder($root->getRightChild());
        }
    }
}

$avl = new AVLTree();
$node = null;
for ($i =1; $i<4; $i++) {
    $avl->insertNode($node, $i);
}

$avl->dfsPreOrder($node);