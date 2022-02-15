<?php

$oEventConfig = new EventConfig();
$oEventBase = new EventBase($oEventConfig);
$iDiy = time();
// public __construct ( EventBase $base , mixed $fd , int $what , callable $cb , mixed $arg = NULL )
$oTimerEvent = new Event($oEventBase, -1, Event::TIMEOUT | Event::PERSIST, function ($iFd, $mWhat, $iDiy) use (&$oTimerEvent) {
    echo "自定义参数：" . $iDiy . "\n";
    $iCounter = mt_rand(1, 3);
    if ($iCounter == 2) {
        var_dump($oTimerEvent->del());
    }
}, $iDiy);

$oTimerEvent->add(0.5);
$oEventBase->loop();