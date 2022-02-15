<?php

namespace CommandPattern\Control;

use CommandPattern\Command\Command;
use CommandPattern\Command\NoCommand\NoCommand;

class RemoteControl
{
    /**@var array $onCommands*/
    public $onCommands;
    /**@var array $offCommands*/
    public $offCommands;
    /**@var integer $slotNum 遥控器插槽数.*/
    public $slotNum;
    /**@var Command $lastCommand 最后一次命令.*/
    public $lastCommand;

    public function __construct($slotNum = 7)
    {
        /**@var NoCommand $noCommand 无命令(防止按压未设置具体命令的按键).*/
        $noCommand = new NoCommand();
        $this->lastCommand = $noCommand;
        for ($i = 0; $i < 7; $i++) {
            $this->onCommands[$i] = $noCommand;
            $this->offCommands[$i] = $noCommand;
        }
    }

    public function setCommand($slot, Command $onCommand, Command $offCommand)
    {
        if ($slot >= 7) {
            return false;
        }

        $this->onCommands[$slot] = $onCommand;
        $this->offCommands[$slot] = $offCommand;
    }

    public function onButtonWasPushed($slot)
    {
        $this->onCommands[$slot]->execute();
        $this->lastCommand = $this->onCommands[$slot];
    }

    public function offButtonWasPushed($slot)
    {
        $this->offCommands[$slot]->execute();
        $this->lastCommand = $this->offCommands[$slot];
    }

    public function undo()
    {
        $this->lastCommand->undo();
    }

}