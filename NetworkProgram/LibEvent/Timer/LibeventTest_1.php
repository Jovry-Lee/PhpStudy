<?php
// 初始化一个空的EventConfig，用这个空的EventConfig初始化一个EventBase
$o_event_config = new EventConfig();
$o_event_base = new EventBase($o_event_config);
// 初始化一个 timer类型的Event
$o_timer_event = new Event($o_event_base, -1, Event::TIMEOUT | Event::PERSIST, function () {
    echo "bingo" . PHP_EOL;
});

// 设置一个超时时间，将事件挂起准备执行
$o_timer_event->add(0.7);
// 让event_base loop起来，相当于while（true）
$o_event_base->loop();