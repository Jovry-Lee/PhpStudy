<?php
$o_event_config = new EventConfig();
// 通过requireFeatures方法来配置控制
// $o_event_config->requireFeatures( EventConfig::FEATURE_ET );
$o_event_config->requireFeatures( EventConfig::FEATURE_O1 );
$o_event_config->requireFeatures( EventConfig::FEATURE_FDS );
$o_event_base = new EventBase( $o_event_config );
// 通过getFeatures获取当前事件base的具体特性
$i_features = $o_event_base->getFeatures();
// 通过&方法，也就是与方法来判断选项是否开启
( $i_features & EventConfig::FEATURE_ET ) and print("ET - edge-triggered IO\n");
( $i_features & EventConfig::FEATURE_O1 ) and print("O1 - O(1) operation for adding/deletting events\n");
( $i_features & EventConfig::FEATURE_FDS ) and print("FDS - arbitrary file descriptor types, and not just sockets\n");
$o_event_base->loop();