<?php

// 所有的命令对象实现相同的接口.
interface Command
{
    public function execute();

    public function undo();
}

class Light
{
    public $location;

    public function __construct($location = '')
    {
        $this->location = $location;
    }

    public function on()
    {
        echo "{$this->location} Light is On\n";
    }

    public function off()
    {
        echo "{$this->location} Light is Off\n";
    }
}

/**
 * 实现一个打开电灯的命令.
 */
class LightOnCommand implements Command
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
        $this->light->on();
    }

    public function undo()
    {
        $this->light->off();
    }
}

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

class Stereo
{
    public $location;

    public function __construct($location = '')
    {
        $this->location = $location;
    }

    public function on()
    {
        echo "{$this->location} Stereo is on.\n";
    }

    public function off()
    {
        echo "{$this->location} Stereo is off.\n";
    }

    public function setCd($cdName)
    {
        echo "{$this->location} Stereo set cd {$cdName}.\n";
    }

    public function setDvd($dvdName)
    {
        echo "{$this->location} Stereo set Dvd {$dvdName}.\n";
    }

    public function setRadio($radioName)
    {
        echo "{$this->location} Stereo set redio {$radioName}.\n";
    }

    public function setVolume($volume)
    {
        echo "{$this->location} Stereo set volume {$volume}.\n";
    }
}

class StereoOnCommand implements Command
{
    /**@var Stereo $stereo*/
    protected $stereo;

    public function __construct(Stereo $stereo)
    {
        $this->stereo = $stereo;
    }

    public function execute()
    {
        $this->stereo->on();
        $this->stereo->setCd('Jordon');
        $this->stereo->setVolume(11);
    }

    public function undo()
    {
        $this->stereo->off();
    }
}

class StereoOffCommand implements Command
{
    /**@var Stereo $stereo*/
    protected $stereo;

    public function __construct(Stereo $stereo)
    {
        $this->stereo = $stereo;
    }

    public function execute()
    {
        $this->stereo->off();
    }

    public function undo()
    {
        $this->stereo->on();
        $this->stereo->setCd('Jordon');
        $this->stereo->setVolume(11);
    }
}

/**
 * 实现一个空命令.
 */
class NoCommand implements Command
{
    /**
     * 执行.
     */
    public function execute(){}

    /**
     * 撤销.
     */
    public function undo(){}
}

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

// 将所有的装置创建在合适的位置.
$livinRoomLight = new Light('Living Room');
$kitchenLight = new Light('Kitchen');
$stereo = new Stereo('Living Room');

// 创建所有的电灯命令对象.
$livingRoomLightOnCommand = new LightOnCommand($livinRoomLight);
$livingRoomLightOffCommand = new LightOffCommand($livinRoomLight);

$kitchenLightOnCommand = new LightOnCommand($kitchenLight);
$kitchenLightOffCommand = new LightOffCommand($kitchenLight);

// 创建音响开关命令.
$stereoOnCommand = new StereoOnCommand($stereo);
$stereoOffCommand = new StereoOffCommand($stereo);

// 创建遥控器.
$remoteControl = new RemoteControl(7);
// 初始化遥控器.
$remoteControl->setCommand(0, $livingRoomLightOnCommand, $livingRoomLightOffCommand);
$remoteControl->setCommand(1, $kitchenLightOnCommand, $kitchenLightOffCommand);
$remoteControl->setCommand(2, $stereoOnCommand, $stereoOffCommand);

$remoteControl->onButtonWasPushed(0);
$remoteControl->offButtonWasPushed(0);
$remoteControl->onButtonWasPushed(1);
$remoteControl->offButtonWasPushed(1);
$remoteControl->onButtonWasPushed(2);
$remoteControl->offButtonWasPushed(2);

// 模拟按未初始化命令的按键.
$remoteControl->onButtonWasPushed(6);

echo "--------------------------\n";

// 测试撤销操作.
$remoteControl->onButtonWasPushed(0);
$remoteControl->undo();








