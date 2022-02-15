<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '../../init.php';

// 将所有的装置创建在合适的位置.
$livinRoomLight = new \CommandPattern\Receiver\Light('Living Room');
$kitchenLight = new \CommandPattern\Receiver\Light('Kitchen');
$cellingFan = new \CommandPattern\Receiver\CellingFan('Living Room');
$garageDoor = new \CommandPattern\Receiver\GarageDoor();
$stereo = new \CommandPattern\Receiver\Stereo('Living Room');

// 创建所有的电灯命令对象.
$livingRoomLightOnCommand = new \CommandPattern\Command\Light\LightOnCommand($livinRoomLight);
$livingRoomLightOffCommand = new \CommandPattern\Command\Light\LightOffCommand($livinRoomLight);

$kitchenLightOnCommand = new \CommandPattern\Command\Light\LightOnCommand($kitchenLight);
$kitchenLightOffCommand = new \CommandPattern\Command\Light\LightOffCommand($kitchenLight);

// 创建吊扇开关命令对象.
$cellingFanOnCommand = new \CommandPattern\Command\CellingFan\CellingFanOnCommand($cellingFan);
$cellingFanOffCommand = new \CommandPattern\Command\CellingFan\CellingFanOffCommand($cellingFan);

// 创建车库开关命令对象.
$garageDoorOpenCommand = new \CommandPattern\Command\GarageDoor\GarageDoorOpenCommand($garageDoor);
$garageDoorCloseCommand = new \CommandPattern\Command\GarageDoor\GarageDoorCloseCommand($garageDoor);

// 创建音响开关命令.
$stereoOnCommand = new \CommandPattern\Command\Stereo\StereoOnCommand($stereo);
$stereoOffCommand = new \CommandPattern\Command\Stereo\StereoOffCommand($stereo);

// 创建遥控器.
$remoteControl = new \CommandPattern\Control\RemoteControl(7);
// 初始化遥控器.
$remoteControl->setCommand(0, $livingRoomLightOnCommand, $livingRoomLightOffCommand);
$remoteControl->setCommand(1, $kitchenLightOnCommand, $kitchenLightOffCommand);
$remoteControl->setCommand(2, $cellingFanOnCommand, $cellingFanOffCommand);
$remoteControl->setCommand(3, $garageDoorOpenCommand, $garageDoorCloseCommand);
$remoteControl->setCommand(4, $stereoOnCommand, $stereoOffCommand);

$remoteControl->onButtonWasPushed(0);
$remoteControl->offButtonWasPushed(0);
$remoteControl->onButtonWasPushed(1);
$remoteControl->offButtonWasPushed(1);
$remoteControl->onButtonWasPushed(2);
$remoteControl->offButtonWasPushed(2);
$remoteControl->onButtonWasPushed(3);
$remoteControl->offButtonWasPushed(3);
$remoteControl->onButtonWasPushed(4);
$remoteControl->offButtonWasPushed(4);

// 模拟按未初始化命令的按键.
$remoteControl->onButtonWasPushed(6);

echo "--------------------------\n";

// 测试撤销操作.
$remoteControl->onButtonWasPushed(0);
$remoteControl->undo();
