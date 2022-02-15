<?php
$host = '0.0.0.0';
$port = 9999;
$listen_socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );// 创建一个tcp socket
socket_bind( $listen_socket, $host, $port );// 将socket bind到IP：port上
socket_listen( $listen_socket ); // 开始监听socket
while( true ){
    $connection_socket = socket_accept( $listen_socket );
    // 从客户端读取信息, MSG_WAITALL的意思就是“阻塞读取客户端消息”，一只要等足够6个字节长度
    $recv_len = socket_recv( $connection_socket, $recv_content, 6, MSG_WAITALL );
    echo "从客户端获取：{$recv_content}，长度是{$recv_len}".PHP_EOL;
    $msg = "helloworld\r\n"; // 向客户端发送一个helloworld
    socket_write( $connection_socket, $msg, strlen( $msg ) );
    socket_close( $connection_socket );
}
socket_close( $listen_socket );