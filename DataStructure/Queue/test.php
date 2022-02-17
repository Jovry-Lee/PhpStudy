<?php

use DataStructure\Queue\Queue;

require_once __DIR__ . "/../../init.php";

$queue = new Queue();
$queue->enqueue("start");
$queue->enqueue(1);
$queue->enqueue(2);
$queue->enqueue(3);
$queue->enqueue(4);
$queue->enqueue("End");

while(!$queue->isEmpty()){
  echo $queue->dequeue()."\n";
}