<?php
$host = '0.0.0.0';
$port = 9999;
// 创建一个tcp socket，底层就是对socket() API的封装
// 第一个参数有AF_INET、AF_INET6、AF_UNIX三种，其实分别就是IPv4、IPv6、文件sock的意思
// 第二个参数有SOCK_STREAM、SOCK_DGRAM、SOCK_RAW等五种，这三种比较常见
// SOCK_STREAM就是流式面向连接的可靠协议，TCP就是基于SOCK_STREAM
// SOCK_DGRAM就是数据包、无连接的不可靠协议，UDP基于SOCK_DGRAM
// SOCK_RAW就是最粗暴原始的那种，你要完全手工来控制，你可以做成面向连接，
// 也可以做成无连接，由你掌控，这种用的比较多的是基于SOCK_RAW实现ping
// 第三个参数共有两个值SOL_TCP、SOL_UDP
// 这里提醒一下就是，后两个参数的选择是有关联性的，比如第二个参你用了
// SOCK_STREAM，那么第三个参数记得用SOL_TCP
// 这里值得注意是：$listen_socket实际上就是一个文件描述符了，也就是fd
$listen_socket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
// 将socket bind到IP：port上
// 这里实际上还是有一些说法的，我这里结合UNP按照自己理解这么来说：
// 就是将$listen_socket协议捆绑到以$host&$port指定的socket上
// 实际上$listen_socket已经实现了一个本地协议
// bind就是要把这个本地协议绑定到指定的网络socket上
socket_bind( $listen_socket, $host, $port );
// 开始监听socket
socket_listen( $listen_socket );
// 进入while循环，不用担心死循环死机，因为程序将会阻塞在下面的socket_accept()函数上
while( true ){
    // 此处将会阻塞住，一直到有客户端来连接服务器。阻塞状态的进程是不会占据CPU的
    // 所以你不用担心while循环会将机器拖垮，不会的
    // 此处请你记住阻塞这个词
    $connection_socket = socket_accept( $listen_socket );
    // 从客户端读取信息
    $content = socket_read( $connection_socket, 4096 );
    echo "从客户端获取：{$content}";
    // 向客户端发送一个helloworld
    $msg = "helloworld\r\n";
    socket_write( $connection_socket, $msg, strlen( $msg ) );
    socket_close( $connection_socket );
}
// 出于礼貌，记得用完了关闭掉...
socket_close( $listen_socket );