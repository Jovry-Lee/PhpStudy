<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '../../init.php';

// 实例化一个遥控器.
$simpleRemoteControl = new \CommandPattern\Control\SimpleRemoteContorl();
// 实例化一个灯.
$light = new \CommandPattern\Receiver\Light();
// 实例化一个开灯的行为.
$lightOnCommand = new \CommandPattern\Command\Light\LightOnCommand($light);
// 遥控器设置开灯行为.
$simpleRemoteControl->setCommand($lightOnCommand);
// 按下遥控器.
$simpleRemoteControl->buttenWasPressed();

// 开车库门.
$garageDoor = new \CommandPattern\Receiver\GarageDoor();
$garageDoorOpenCommand = new \CommandPattern\Command\GarageDoor\GarageDoorOpenCommand($garageDoor);
$simpleRemoteControl->setCommand($garageDoorOpenCommand);
$simpleRemoteControl->buttenWasPressed();
