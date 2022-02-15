<?php

interface Iterator
{
    // 告知当前集合中是否还有更多元素。
    public function hasNext();
    // 返回集合中下一个对象。
    public function next();
}




class IteratorPattern
{

}