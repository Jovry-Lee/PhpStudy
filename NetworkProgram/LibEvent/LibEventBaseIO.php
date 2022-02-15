<?php
// 第一步：创建好一个非阻塞的listen socket。
$host = '0.0.0.0';
$port = '6666';
$listenSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($listenSocket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_option($listenSocket, SOL_SOCKET, SO_REUSEPORT, 1);
socket_bind($listenSocket, $host, $port);
socket_listen($listenSocket);
// 设置非阻塞。
socket_set_nonblock($listenSocket);

// 第二步：在listen socket上创建一个持久的读事件。
$eventArr = [];
$clientArr = [];

$evenBase = new EventBase();
$methodName = $evenBase->getMethod();
echo "methodName：" . $methodName . PHP_EOL;

$event = new Event(
    $evenBase,
    $listenSocket,
    Event::READ | Event::PERSIST,
    function ($fd, $what, $evenBase) use (&$eventArr, &$clientArr) {
        // 第三步：当listen socket发现可读事件后就执行socket_accept操作。
        $connectSocket = socket_accept($fd);
        // 保存客户端连接socket，若不保存，客户端将一连接上就会被关闭。？？？？？？？？？？？？为什么会这样呢？
        $clientArr[] = $connectSocket;
        echo "客户端建立连接：{$connectSocket}" . PHP_EOL;

        // 第四步：为客户端连接socket添加持久读取事件
        $event = new Event(
            $evenBase,
            $connectSocket,
            Event::READ | Event::PERSIST,
            function ($fd) {
                // 第五步：从客户端socket读取数据。
                $content = socket_read($fd, 1024);
                echo $content;
            }
        );
        $event->add();
        $eventArr[] = $event;
    }, $evenBase);

$event->add();
// 将事件保存到事件数组，若不保存，则事件将会丢失。
$eventArr[] = $event;
$evenBase->loop();