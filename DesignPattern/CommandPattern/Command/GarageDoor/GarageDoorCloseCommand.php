<?php

namespace CommandPattern\Command\GarageDoor;

use \CommandPattern\Command\Command;
use CommandPattern\Receiver\GarageDoor;

class GarageDoorCloseCommand implements Command
{
    /** @var GarageDoor $garageDoor*/
    protected $garageDoor;

    public function __construct(GarageDoor $garageDoor)
    {
        $this->garageDoor = $garageDoor;
    }

    public function execute()
    {
        $this->garageDoor->down();
        $this->garageDoor->lightOff();
        $this->garageDoor->stop();
    }

    public function undo()
    {
        $this->garageDoor->up();
        $this->garageDoor->lightOn();
    }
}