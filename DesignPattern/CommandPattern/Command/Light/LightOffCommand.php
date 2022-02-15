<?php

namespace CommandPattern\Command\Light;

use \CommandPattern\Command\Command;
use \CommandPattern\Receiver\Light;

/**
 * 实现一个打开电灯的命令.
 */
class LightOffCommand implements Command
{
    /**@var Light*/
    public $light;

    /**
     * 构造器
     *
     * @param Light $light 某个电灯的实例(例如,客厅的电灯).
     */
    public function __construct(Light $light)
    {
        $this->light = $light;
    }

    /**
     * 执行(以light作为执行的接收者).
     */
    public function execute()
    {
        $this->light->off();
    }

    public function undo()
    {
        $this->light->on();
    }
}

