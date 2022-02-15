<?php

namespace CommandPattern\Command;

// 所有的命令对象实现相同的接口.
interface Command
{
    public function execute();

    public function undo();
}