#### 1. 创建一个套接字
`socket_create ( int $domain , int $type , int $protocol ) : Socket|false`
- $domain：有AF_INET、AF_INET6、AF_UNIX三种，其实分别表示IPv4、IPv6、文件sock；
- $type：有SOCK_STREAM、SOCK_DGRAM、SOCK_RAW等五种，这三种比较常见：
    - SOCK_STREAM: 就是流式面向连接的可靠协议，TCP就是基于SOCK_STREAM
    - SOCK_DGRAM: 就是数据包、无连接的不可靠协议，UDP基于SOCK_DGRAM
    - SOCK_RAW: 就是最粗暴原始的那种，需要完全手工来控制，可以做成面向连接，也可以做成无连接，这种用的比较多的是基于SOCK_RAW实现ping
- $protocol：常用SOL_TCP、SOL_UDP；  
（注：后两个参数的选择是有关联性的，如第二个参用了SOCK_STREAM，那么第三个参数记得用SOL_TCP）

#### 2. 将套接字绑定到IP:Port上
`socket_bind ( Socket $socket , string $address , int $port = 0 ) : bool`  
将$socket协议捆绑到以$address&$port指定的socket上，实际上$socket已经实现了一个本地协议,bind就是要把这个本地协议绑定到指定的网络socket上。

#### 3. 监听套接字上的连接
`socket_listen ( Socket $socket , int $backlog = 0 ) : bool`

#### 4. 接收套接字上的连接
`socket_accept ( Socket $socket ) : Socket|false`
（注：该方法将会阻塞住，一直到有客户端来连接服务器。阻塞状态的进程是不会占据CPU的。）

==直接使用`socket_accept`方法阻塞获取连接有什么问题？== 
答：假设一个客户端发起连接后服务器收到连接并把该请求放到了**请求队列**里，但是此时服务器还尚未执行accept，与此同时客户端却又断开了连接，然后很快服务器拿出队列里这个请求开始accept，由于这个$socket是阻塞的，服务器就会阻塞在accept这里一直等，但是客户端实际上已经断开连接了。

**解决方案**：  
通过异步非阻塞方式，将监听的socket加入到IO多路复用事件中，当可读事件到来时IO复用通知调用方，此时再去accept就一定能获取到新的客户端。


`socket_accept`实际上在同一时刻只能供一个客户端使用，此时可通过`pre-fork`方式fork出固定数量的进程进行处理。  
`pre-fork`好处在于：
- 提前保护好系统，进程固定；
- 其次是这个pre-fork的数量是根据业务繁忙程度预估的，因此响应用户也不会有大问题。
    - 预先fork，避免系统在遭遇突发访问时不断fork导致系统资源爆炸
    - fork后的进程不会在处理完成后退出，而是接着服务下一个客户端，避免了反复fork。

示例：pre-fork
```multiPackServer.php
<?php
$host = '0.0.0.0';
$port = 9999;
$processNums = 10;

$listen_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($listen_socket, $host, $port);
socket_listen($listen_socket);

// 固定fork进程数量。
for ($i = 0; $i < $processNums; $i++) {
    $pid = pcntl_fork();
    if ($pid != 0) {
        continue;
    }

    $myPid = getmypid();
    while (true) {
        $connection_socket = socket_accept($listen_socket);
        // 从客户端读取信息,数据包是由 header+body 组成的,先读取header，header中包含剩余body体的具体长度
        socket_recv($connection_socket, $recv_content, 4, MSG_WAITALL);  // 这里使用用4呢，因为pack()里的N就是32位无符号整数。
        // Nlen中N是固定的，表示32位无符号证书，len可以随意换别的，比如Nval。
        $body_len_pack = unpack("Nlen", $recv_content);

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
```

#### 5. 从客户端读取数据
##### 5.1 socket_read
`socket_read ( Socket $socket , int $length , int $mode = PHP_BINARY_READ ) : string|false`
- $socket：通过`socket_create()`或`socket_accept()`创建的有效Socket资源。
- $length：通过length参数指定最大读取字节数，另外可以使用`\r`, `\n`, or `\0`作为结束读取的标识（依赖于以下类型参数）
- $mode：可选类型参数是一个命名常量
    - PHP_BINARY_READ (默认) - 使用系统`recv()`函数（二进制安全）。
    - PHP_NORMAL_READ - 使用`\n`或`\r`作为读截止符。

##### 5.2 socket_recv
`socket_recv ( Socket $socket , string|null &$data , int $length , int $flags ) : int|false`
- $socket：参数 socket 必须是一个由 socket_create() 创建的socket资源。
- $data：从socket中获取的数据将被保存在由 $data 指定的变量中。如果有错误发生，如链接被重置，数据不可用等等， $data 将被设为 NULL。
- $length：长度最多为 length 字节的数据将被接收。
- $flags：flags 的值可以为下列任意flag的组合。使用按位或运算符(|)来 组合不同的flag。
    - MSG_OOB       处理超出边界的数据。MSG_OOB其实叫做外带数据，有时候有些地方叫做**紧急数据**。例如，由于TCP数据有些时候会被分拆成好几个数据块（假如说3
    个），然后服务器方收到数据后需要按照序号去排列好手上的数据块，如果此时有个紧急的数据期望能够送到服务器，那么发送方只需要在send数据数据时加上一个MSG_OOB的标记，服务器就会优先接受这个数据，不用等候服务器按顺序收好那3个普通数据块。
    - MSG_PEEK      从接受队列的起始位置接收数据，但不将他们从接受队列中移除。MSG_PEEK则有这样一个作用，假设有一数据" lalala-password "，如果服务器在recv()接受数据时发现数据包中带有MSG_PEEK标记，那么TA会先读取完毕lalala遇到`-`停止（这个`-`是自定义的），虽然读取数据了，但是这坨数据不会从TCP接受缓冲区被清除掉，TA还会留在那里，等你下次再次使用recv()接受，TA就会接着从`-`位置读取剩下的“ password ”。这个功能主要用于**预探测功能**，意思是先读取一次数据，可以从第一次读取的数据（本次数据中可以包含关于剩余那坨数据的主要信息），然后根据本次数据的信息来让程序做决定下次recv()是执行还是不执行，如果执行了，是否需要走什么其他特殊逻辑。
    - MSG_WAITALL   在接收到至少 len 字节的数据之前，造成一个阻塞，并暂停脚本运行（block）。但是， 如果接收到中断信号，或远程服务器断开连接，该函数将返回少于 len 字节的数据。
    - MSG_DONTWAIT  如果指定了该flag，函数将不会造成阻塞，即使在全局设置中指定了阻塞设置。
    
返回值：返回接收的字节数或false，若出现错误，可通过socket_last_error()获取错误码，该错误码可作为参数传入socket_strerror()获取文本解释。

示例-MSG_WAITALL：
``` socketRecv.php
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
```
测试:
```终端1
 % php socketRecv.php 
从客户端获取：abcdef，长度是6
```

```终端2
% telnet 127.0.0.1 9999
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
abcdefghijk
helloworld
Connection closed by foreign host.
```

示例-MSG_DONTWAIT
```socketRecvDontWait.php
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
```

测试：
```终端1
% php socketRecvDontWait.php
0:8
0:8
0:8
0:8
0:8
0:8
0:8
0:8
8:8
从客户端获取：abcdefg
```

```终端2
% telnet 127.0.0.1 9999
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
abcdefg
helloworld
Connection closed by foreign host.
```

#### 6. 向客户端写数据
`socket_write ( Socket $socket , string $data , int|null $length = null ) : int|false`
- $socket：通过`socket_create()`或`socket_accept()`创建的有效Socket资源；
- $data：要写入缓冲区的数据；
- $length(可选)：指定写入缓冲区数据的字节长度，若大于缓冲区最大长度，将按缓冲区长度进行截取。

返回值：返回成功写入socket的字节数或者false。若出现错误，可通过socket_last_error()获取错误码，该错误码可作为参数传入socket_strerror()获取文本解释。

#### 7. 关闭套接字
`socket_close ( Socket $socket ) : void`

#### 8. 扩展：Pack/Unpack数据打包/拆包函数
pack/unpack示例：
```packServer.php
<?php
$host = '0.0.0.0';
$port = 9999;
$listen_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($listen_socket, $host, $port);
socket_listen($listen_socket);
while (true) {
    $connection_socket = socket_accept($listen_socket);

    // 从客户端读取信息,数据包是由 header+body 组成的,先读取header，header中包含剩余body体的具体长度
    socket_recv($connection_socket, $recv_content, 4, MSG_WAITALL);  // 这里使用用4呢，因为pack()里的N就是32位无符号整数。
    // Nlen中N是固定的，表示32位无符号证书，len可以随意换别的，比如Nval。
    $body_len_pack = unpack("Nlen", $recv_content);
    // 打印一下bodyLenPack的值进行观测。
    echo "body_len_pack value: \n";
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
```

```packClient.php
<?php
$host = "127.0.0.1";
$port = 9999;
$content = "12345678123456781234567812345678";
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$conn = socket_connect($socket, $host, $port);
// payload = header + body
$body = pack("a*", $content);
$body_len = strlen($body);
echo $body . ' | ' . $body_len . PHP_EOL;
// pack()的N参数就表示按照网络字节序打包
$header = pack("N", $body_len);
$payload = $header . $body;
$send_ret = socket_write($socket, $payload, strlen($payload));
var_dump($send_ret);
```

运行结果：
```server端
 % php packServer.php 
body_len_pack value: 
array(1) {
  ["len"]=>
  int(32)
}
从客户端收到：12345678123456781234567812345678
```

```client端
 % php packClient.php 
12345678123456781234567812345678 | 32
int(36)
```