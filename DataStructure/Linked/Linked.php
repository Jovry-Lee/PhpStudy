<?php

namespace DataStructure\Linked;

require_once 'Node.php';

class Linked
{
    public $head;
    public $pre;
    public function __construct(array $linkData)
    {
        $pre = $this->head = new Node();
        foreach ($linkData as $value) {
            $pre = $pre->next = new Node($value);
        }
    }

    /**
     * Search:在以head为头结点的链表上计数元素x的个数.
     *
     * @param Node  $head 头结点.
     * @param mixed $x    元素.
     *
     * @return integer
     */
    public function search(Node $head, $x)
    {
        $count = 0;
        $p = $head->next;
        while (!empty($p)) {
            if ($p->data == $x) {
                $count++;
            }
            $p = $p->next;
        }
        return $count;
    }

    /**
     * Insert:在以head为头结点的链表的第pos个为上插入x.
     *
     * @param Node    $head 头结点.
     * @param integer $pos  插入位置.
     * @param mixed   $x    元素.
     *
     * @return void
     */
    public function insert(Node &$head, $pos, $x)
    {
        $p = $head;
        // 定位到插入位置的前一个结点.
        for($i = 0; $i < $pos - 1; $i++) {
            $p = $p->next;
        }
        // 新建结点.
        $q = new Node($x);
        $q->next =$p->next;
        $p->next = $q;
    }

    /**
     * Delete:删除以head为头结点的链表中所有数据域为x的结点.
     *
     * @param Node  $head 头结点.
     * @param mixed $x    元素.
     *
     * @return void
     */
    public function delete(Node &$head, $x)
    {
        $p = $head->next;
        $pre = $head;
        while(!empty($p))
        {
            // 数据域恰好为x,说明要删除该结点.
            if ($p->data == $x) {
                $pre->next = $p->next;
                $p = $pre->next;
            } else {
                $pre = $p;
                $p = $p->next;
            }
        }
    }
}
