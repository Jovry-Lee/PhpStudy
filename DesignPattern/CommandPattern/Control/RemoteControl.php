<?php

namespace CommandPattern\Control;

use CommandPattern\Command\Command;
use CommandPattern\Command\NoCommand\NoCommand;

class RemoteControl
{
    /**@var array $onCommands 开命令. */
    public $onCommands;
    /**@var array $offCommands 关命令. */
    public $offCommands;
    /**@var integer $slotNum 遥控器插槽数.*/
    public $slotNum;
    /**@var Command $lastCommand 最后一次命令.*/
    public $lastCommand;

    /**
     * 构造器。
     *
     * @param int $slotNum 槽数量。
     *
     */
    public function __construct($slotNum = 7)
    {
        $this->initCommands($slotNum);
        $this->slotNum = $slotNum;
    }

    /**
     * 初始化命令。
     */
    private function initCommands($slotNum)
    {
        /**@var NoCommand $noCommand 无命令(防止按压未设置具体命令的按键).*/
        $noCommand = new NoCommand();
        $this->lastCommand = $noCommand;
        for ($i = 0; $i < $slotNum; $i++) {
            $this->onCommands[$i] = $noCommand;
            $this->offCommands[$i] = $noCommand;
        }
    }

    /**
     * 设置命令。
     *
     * @param int $slot 槽位置。
     * @param Command $onCommand 开命令。
     * @param Command $offCommand 关命令。
     */
    public function setCommand($slot, Command $onCommand, Command $offCommand)
    {
        if ($slot >= $this->slotNum) {
            return;
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