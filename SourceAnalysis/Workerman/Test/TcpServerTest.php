<?php

use Workerman\Worker;

require_once __DIR__ . '/../vendor/autoload.php';

/*
// #### create socket and listen 1234 port ####
$tcp_worker = new Worker('tcp://0.0.0.0:1234');

// 4 processes
$tcp_worker->count = 4;

// Emitted when new connection come
$tcp_worker->onConnect = function ($connection) {
    echo "New Connection\n";
};

// Emitted when data received
$tcp_worker->onMessage = function ($connection, $data) {
    // Send data to client
    $connection->send("Hello $data \n");
};

// Emitted when connection is closed
$tcp_worker->onClose = function ($connection) {
    echo "Connection closed\n";
};
*/

// 设置4个worker。
for ($i = 1; $i <= 4; $i++) {
    $workerName = 'tcpWorker' . $i;
    $$workerName = new Worker('tcp://0.0.0.0:' . (1230 + $i));
    $$workerName->count = $i;

    // Emitted when new connection come
    $$workerName->onConnect = function ($connection) {
        echo "New Connection\n";
    };

    // Emitted when data received
    $$workerName->onMessage = function ($connection, $data) {
        // Send data to client
        $connection->send("Hello $data \n");
    };

    // Emitted when connection is closed
    $$workerName->onClose = function ($connection) {
        echo "Connection closed\n";
    };
}


Worker::runAll();