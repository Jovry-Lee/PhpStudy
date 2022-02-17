<?php

namespace DataStructure\Linked;

/**
 * 链表结点。
 */
class Node
{
    /** @var mixed $data 数据域*/
    public $data;
    /** @var Node $next 指针域*/
    public $next;

    public function __construct($x = null)
    {
        $this->data = $x;
        $this->next = null;
    }

    /**
     * Set data.
     *
     * @param mixed $data 数据域.
     *
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data.
     *
     * @param Node $next 指针域.
     *
     * @return void
     */
    public function setNext($next)
    {
        $this->next = $next;
    }

    /**
     * Get next.
     *
     * @return Node
     */
    public function getNext()
    {
        return $this->next;
    }
}

// test2.
$head = new Node();
$head->next = null;




// test1.
$node1 = new Node();
$node2 = new Node();
$node3 = new Node();
$node4 = new Node();
$node5 = new Node();

$node1->data = 5;
$node1->next = $node2;

$node1->data = 3;
$node1->next = $node3;

$node1->data = 6;
$node1->next = $node4;

$node1->data = 1;
$node1->next = $node5;

$node1->data = 2;
$node1->next = null;
