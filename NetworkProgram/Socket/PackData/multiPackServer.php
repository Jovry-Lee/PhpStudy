<?php
$host = '0.0.0.0';
$port = 9999;
$processNums = 1;

$listen_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($listen_socket, $host, $port);
socket_listen($listen_socket);


$masterPid = posix_getpid();
echo "Master PID: {$masterPid}\n";

for ($i = 0; $i < $processNums; $i++) {
    $pid = pcntl_fork();
    if ($pid != 0) {
        continue;
    }

    $myPid = getmypid();
    echo "Worker PID：{$myPid}\n";
    while (true) {
        $connection_socket = socket_accept($listen_socket);

        // 获取客户端信息。
        socket_getpeername($connection_socket, $clientAddr, $clientPort);
        echo "WorkerPid：{$myPid}\tClient INFO：{$clientAddr}:{$clientPort}\n";

        // 从客户端读取信息,数据包是由 header+body 组成的,先读取header，header中包含剩余body体的具体长度
        socket_recv($connection_socket, $recv_content, 4, MSG_WAITALL);  // 这里使用用4呢，因为pack()里的N就是32位无符号整数。
        // Nlen中N是固定的，表示32位无符号整数，len可以随意换别的，比如Nval。
        $body_len_pack = unpack("Nlen", $recv_content);
        echo "bodyLenPack：\t" . json_encode($body_len_pack) . "\n";

        $body_len = $body_len_pack['len'];
        // 有了body的长度，再使用socket_recv()指定长度读取即可
        socket_recv($connection_socket, $recv_content, $body_len, MSG_WAITALL);
        echo "{$myPid}\t从客户端收到：{$recv_content}" . PHP_EOL;

        // 向客户端发送一个helloworld
        $msg = "helloworld\r\n";
        socket_write($connection_socket, $msg, strlen($msg));
        sleep(20);
        socket_close($connection_socket);
    }
}

while (true) {
    sleep(1);
}

socket_close($listen_socket);