<?php
echo getmypid() . PHP_EOL;
$o_event_config = new EventConfig();
$o_event_base = new EventBase( $o_event_config );
$o_timer_event = new Event( $o_event_base, SIGTERM, Event::SIGNAL | Event::PERSIST, function() {
    echo "sigterm".PHP_EOL;
} );
$o_timer_event->add();
$o_event_base->loop();
