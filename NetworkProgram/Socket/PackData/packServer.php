<?php
$host = '0.0.0.0';

$port = 9999;
// 创建一个tcp socket
$listen_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
// 将socket bind到IP：port上
socket_bind($listen_socket, $host, $port);
// 开始监听socket
socket_listen($listen_socket);
while (true) {
    // 所以你不用担心while循环会将机器拖垮，不会的
    $connection_socket = socket_accept($listen_socket);

    // 从客户端读取信息
    // 数据包是由 header+body 组成的
    // 先读取header，header中包含剩余body体的具体长度
    // 这里为什么用4呢？因为pack()里的N就是32位无符号整数
    socket_recv($connection_socket, $recv_content, 4, MSG_WAITALL);
    // Nlen是啥意思？N是固定的，但是len可以随意换别的，比如Nval
    // 然后我建议你打印一下$body_len_pack就知道了
    $body_len_pack = unpack("Nlen", $recv_content);
    var_dump($body_len_pack);
    $body_len = $body_len_pack['len'];
    // 有了body的长度，再使用socket_recv()指定长度读取即可
    socket_recv($connection_socket, $recv_content, $body_len, MSG_WAITALL);
    echo "从客户端收到：{$recv_content}" . PHP_EOL;

    // 向客户端发送一个helloworld
    $msg = "helloworld\r\n";
    socket_write($connection_socket, $msg, strlen($msg));
    socket_close($connection_socket);
}

socket_close($listen_socket);