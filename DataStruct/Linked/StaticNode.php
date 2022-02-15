<?php

namespace DataStructure\Linked;

/**
 * 静态链表结点。
 */
class StaticNode
{
    /** @var mixed $data 数据域*/
    public $data;
    /** @var integer $next 指针域*/
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
     * @param integer $next 指针域.
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
     * @return integer
     */
    public function getNext()
    {
        return $this->next;
    }
}