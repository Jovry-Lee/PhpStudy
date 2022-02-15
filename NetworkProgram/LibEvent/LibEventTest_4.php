<?php
// 查看当前支持的IO复用方法
echo "System support methods：\n";
print_r( Event::getSupportedMethods() );

// 查看默认情况下Libevent使用哪个IO复用
$o_event_base = new EventBase();
echo "default IO method：" . $o_event_base->getMethod().PHP_EOL;

// 某些情况下我们就只需要指定使用poll
$o_event_config = new EventConfig();
$o_event_config->avoidMethod( "select" );
$o_event_config->avoidMethod( "epoll" );
$o_event_base = new EventBase( $o_event_config );
echo $o_event_base->getMethod().PHP_EOL;
$o_event_base->loop();