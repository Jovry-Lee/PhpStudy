<?php

namespace Arithmetic\Sort;

interface ISort
{
    /**
     * 正序。
     */
    public function sort(array &$arr) :?bool;

    /**
     * 逆序。
     */
    public function rsort(array &$arr) :?bool;
}