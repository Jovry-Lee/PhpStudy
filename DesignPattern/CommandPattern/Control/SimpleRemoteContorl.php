<?php

namespace CommandPattern\Control;
use \CommandPattern\Command\Command;

class SimpleRemoteContorl
{
    /**
     * 命令槽, 持有某个命令.
     *
     * @var Command
     */
    protected $slot;

    public function setCommand(Command $command)
    {
        $this->slot = $command;
    }

    public function buttenWasPressed()
    {
        $this->slot->execute();
    }
}