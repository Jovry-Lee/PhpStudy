<?php

class SelectServer
{
    private $host = '0.0.0.0';
    private $port = '6666';
    private $listenSocket;
    private $clientArr = [];
    private $closureArr = [];

    public function init()
    {
        $listenSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($listenSocket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_option($listenSocket, SOL_SOCKET, SO_REUSEPORT, 1);
        socket_set_nonblock($listenSocket);
        socket_bind($listenSocket, $this->host, $this->port);
        socket_listen($listenSocket);
        $this->clientArr = [$listenSocket];
        $this->listenSocket = $listenSocket;
    }

    public function run()
    {
        while (true) {
            $read = $this->clientArr;
            $write = [];
            $exception = [];
            $ret = socket_select($read, $write, $exception, null);
            if ($ret <= 0) {
                continue;
            }

            if (in_array($this->listenSocket, $read)) {
                $connectSocket = socket_accept($this->listenSocket);
                if (!$connectSocket) {
                    continue;
                }
                $this->clientArr[] = $connectSocket;
                $key = array_search($this->listenSocket, $read);
                unset($read[$key]);

                $connectFunc = $this->closureArr['connect'] ?? false;
                if ($connectFunc !== false) {
                    call_user_func_array($connectFunc, []);
                }
            }

            foreach ($read as $readKey => $readFd) {
                socket_recv($readFd, $content, 1024, 0);
                if ($content && isset($this->closureArr['message'])) {
                    call_user_func_array($this->closureArr['message'], [$content]);
                }

                unset($this->clientArr[$readKey]);
                socket_shutdown($readFd);
                socket_close($readFd);
            }
        }
    }

    public function on($eventName, Closure $func)
    {
        $this->closureArr[$eventName] = $func;
    }
}

$server = new SelectServer();
$server->init();
$server->on('connect', function () {
    echo "触发connect" . PHP_EOL;
});

$server->on('message', function ($data) {
    echo "触发message，收到数据：" . $data . PHP_EOL;
});
$server->run();