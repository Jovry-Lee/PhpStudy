<?php
$timeout = 2;
$o_event_config = new EventConfig();
$o_event_base = new EventBase($o_event_config);
$event_timer = Event::timer($o_event_base, function ($timeout) use (&$event_timer) {
    echo "$timeout seconds elapsed\n";
    // $event_timer->del(); // 只执行一次
    $event_timer->addTimer($timeout); // 将定时触发
}, $timeout);
$event_timer->addTimer($timeout);
$o_event_base->loop();