<?php
$host = '0.0.0.0';
$port = '6666';
$listenSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($listenSocket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_option($listenSocket, SOL_SOCKET, SO_REUSEPORT, 1);
socket_bind($listenSocket, $host, $port);
socket_listen($listenSocket);
// 设置非阻塞。
socket_set_nonblock($listenSocket);

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
        $connectSocket = socket_accept($fd);
        $clientArr[] = $connectSocket;
        echo "客户端建立连接：{$connectSocket}" . PHP_EOL;

        /* 测试Write事件。
        $event = new Event(
            $evenBase,
            $connectSocket,
            Event::WRITE | Event::PERSIST,
            function ($fd) {
                echo "Event::write回调".PHP_EOL;
                sleep(1);
            }
        );
        $event->add();
        $eventArr[] = $event;
        */

        // 从客户端上读取数据。
        $event = new Event(
            $evenBase,
            $connectSocket,
            Event::READ | Event::PERSIST,
            function ($connectSocket, $what, $evenBase) use (&$eventArr, &$clientArr) {
                $content = socket_read($connectSocket, 1024);
                echo "{$connectSocket}发送消息：{$content}" . PHP_EOL;

                // 处理读事件。
                $writeEvent = new Event(
                    $evenBase,
                    $connectSocket,
                    Event::WRITE | Event::PERSIST,
                    function ($connectSocket) use (&$eventArr, &$clientArr, $content) {
                        echo "{$connectSocket}开始群发消息" . PHP_EOL;
                        foreach ($clientArr as $targetClient) {
                            if (intval($targetClient) != intval($connectSocket)) {
                                socket_write($targetClient, $content, strlen($content));
                            }
                        }
                        // 写回调逻辑执行完毕后，将写事件删除。
                        $oEvent = $eventArr[intval($connectSocket)]['write'];
                        $oEvent->del();
                        unset($eventArr[intval($connectSocket)]['write']);
                    }
                );
                $writeEvent->add();
                $eventArr[intval($connectSocket)]['write'] = $writeEvent;
            },
            $evenBase
        );
        $event->add();
        $eventArr[intval($connectSocket)]['read'] = $event;

    }, $evenBase);

$event->add();
// $eventArr[] = $event;
$evenBase->loop();