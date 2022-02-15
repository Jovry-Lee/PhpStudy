<?php

namespace CommandPattern\Command\NoCommand;

use \CommandPattern\Command\Command;

/**
 * 实现一个打开电灯的命令.
 */
class NoCommand implements Command
{
    /**
     * 执行.
     */
    public function execute()
    {

    }

    // 撤销.
    public function undo()
    {

    }
}

