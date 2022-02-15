<?php
require_once "./Http.php";

$host = '0.0.0.0';
$port = 6666;
$listen_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
// ip及port复用。
socket_set_option($listen_socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_option($listen_socket, SOL_SOCKET, SO_REUSEPORT, 1);
// 绑定套接字。
socket_bind($listen_socket, $host, $port);
// 监听套接字。
socket_listen($listen_socket);
// 设置非阻塞。
socket_set_nonblock($listen_socket);

socket_getsockname($listen_socket, $addr, $port);
echo 'Select HTTP Server - ' . $addr . ':' . $port . PHP_EOL;

$client = array($listen_socket);
while (true) {
    $read = $client;
    $write = array();
    $exception = array();
    $ret = socket_select($read, $write, $exception, null);
    echo "select-loop : {$ret}" . PHP_EOL . PHP_EOL . PHP_EOL;
    print_r($read);
    if ($ret <= 0) {
        continue;
    }
    // listen-socket有读事件，表示有新的客户端来了.客户端先建立连接再进行数据传输。
    if (in_array($listen_socket, $read)) {
        echo "开始处理客户端连接~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
        $connection_socket = socket_accept($listen_socket); // 获取客户端连接套接字。
        if (!$connection_socket) {
            continue;
        }
        socket_getpeername($connection_socket, $client_ip, $client_port);
        echo "Client {$client_ip}:{$client_port}".PHP_EOL;
        $client[] = $connection_socket;
        $key = array_search($listen_socket, $read);
        unset($read[$key]);
        echo "结束处理客户端连接~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
    }

    // 对于其他socket
    foreach ($read as $read_key => $read_fd) {
        echo "开始处理数据传输~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
        // 注意！这种获取HTTP数据的方式并不正确，这种写法只能获取固定2048长度的数据
        // 正规正确的写法应该是通过content-length或者chunk size来获取完整http原始数据
        $ret = socket_recv($read_fd, $recv_content, 2048, 0);
        echo $recv_content;
        $decode_ret = Http::decode($recv_content);
        print_r($decode_ret);

        $encode_ret = Http::encode(array(
            'username' => "wahaha",
        ));
        socket_write($read_fd, $encode_ret, strlen($encode_ret));

        //socket_shutdown( $read_fd );
        socket_close($read_fd);
        unset($read[$read_key]);
        $key = array_search($read_fd, $client);
        unset($client[$read_key]);
        echo "结束处理数据传输~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n";
    }
}