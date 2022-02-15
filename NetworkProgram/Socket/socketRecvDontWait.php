<?php
$host = '0.0.0.0';
$port = 9999;
$listen_socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
socket_bind( $listen_socket, $host, $port );
socket_listen( $listen_socket );
while( true ){
    $connection_socket = socket_accept( $listen_socket );
    // 从客户端读取信息
    $total_len = 8;
    $recv_len  = 0;
    $recv_content = '';
    // 用了MSG_DONTWAIT所以会立马往下执行
    $len = socket_recv( $connection_socket, $content, $total_len, MSG_DONTWAIT );
    // 到了while后，一旦客户端连接上来，就会不断循环
    while ( $recv_len < $total_len ) {
        $len = socket_recv( $connection_socket, $content, ( $total_len - $recv_len ), MSG_DONTWAIT );
        $recv_len = $recv_len + $len;
        // 人为添加一行sleep(1)来进行观察。
        sleep(1);
        echo $recv_len.':'.$total_len.PHP_EOL;
        if ( $recv_len > 0 ) {
            $recv_content = $recv_content.$content;
        }
    }
    echo "从客户端获取：{$recv_content}";
    // 向客户端发送一个helloworld
    $msg = "helloworld\r\n";
    socket_write( $connection_socket, $msg, strlen( $msg ) );
    socket_close( $connection_socket );
}
socket_close( $listen_socket );