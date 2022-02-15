### 1. 简介
### 2. Stream方法详解
#### 2.1 stream_socket_pair
##### 2.1.1 功能
创建一对完全一样的网络套接字连接流.这个函数通常会被用在进程间通信(Inter-Process Communication).这对套接字可以进行双工通信，每一个描述符既可以读也可以写.
##### 2.1.2 说明
stream_socket_pair ( int $domain , int $type , int $protocol ) : array

入参:
- $domain
使用的协议族： STREAM_PF_INET(Internet协议版本4(IPv4)), STREAM_PF_INET6(Internet协议版本6(IPv6)) or STREAM_PF_UNIX(Unix系统内部协议)
- type
通信类型: STREAM_SOCK_DGRAM(提供无连接消息(例如UDP)的数据报), STREAM_SOCK_RAW(提供原始套接字，它提供对内部网络协议和接口的访问。通常这种类型的套接字只对根用户可用), STREAM_SOCK_RDM(提供一个RDM(可靠传递的消息)套接字), STREAM_SOCK_SEQPACKET(提供一个按顺序排列的包流套接字) or STREAM_SOCK_STREAM(为带外数据(例如TCP)提供有序的双向字节流和传输机制)
- protocol
用的传输协议: STREAM_IPPROTO_ICMP(提供ICMP套接字), STREAM_IPPROTO_IP(提供一个IP套接字), STREAM_IPPROTO_RAW(提供原始套接字), STREAM_IPPROTO_TCP(提供TCP套接字) or STREAM_IPPROTO_UDP(提供UDP套接字)

返回值:
如果成功将返回一个数组包括了两个socket资源，错误时返回FALSE.
##### 2.1.3 示例
stream_socket_pair用于进程间通信,
```
stream_socket_pair.php
<?php

$sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
$pid     = pcntl_fork();

if ($pid == -1) {
    die('could not fork');

} else if ($pid) {
    /* parent */
    fclose($sockets[0]);

    fwrite($sockets[1], "child PID: $pid\n");
    echo fgets($sockets[1]);

    fclose($sockets[1]);

} else {
    /* child */
    fclose($sockets[1]);

    fwrite($sockets[0], "message from child\n");
    echo fgets($sockets[0]);

    fclose($sockets[0]);
}

?>
```
测试:
```
cdyf@jumei:~/tutorial/Stream$ php stream_socket_pair.php
message from child
child PID: 3737
```

#### 2.2 stream_set_blocking
##### 2.2.1 功能
为资源流设置阻塞或者非阻塞模式.此函数适用于支持非阻塞模式的任何资源流（常规文件，套接字资源流等）。
##### 2.2.2 说明
stream_set_blocking ( resource $stream , bool $mode ) : bool

入参:
- $stream
资源流
- $mode
    - 0: 资源流将会被转换为非阻塞模式.
    - 1: 资源流将会被转换为阻塞模式.
该参数的设置将会影响到像 fgets() 和 fread() 这样的函数从资源流里读取数据。
    - 在非阻塞模式下，调用fgets()总是会立即返回；
    - 而在阻塞模式下，将会一直等到从资源流里面获取到数据才能返回。

返回值:
成功时返回 TRUE， 或者在失败时返回 FALSE。

#### 2.3 stream_select
##### 2.3.1 功能
函数的作用是:接受流数组并等待它们改变状态。它的操作与socket_select()函数的操作相同，只是它作用于流。
##### 2.3.2 说明
```
stream_select ( array &$read , array &$write , array &$except , int $tv_sec [, int $tv_usec = 0 ] ) : int
```
入参:
- $read
read数组中列出的流将被监视，以查看是否可以读取字符(更准确地说，查看读取是否不会阻塞—特别是，流资源在文件末尾也准备好了，在这种情况下，fread()将返回一个零长度的字符串)。
- $write
写入数组中列出的流将被监视，以查看写入是否会阻塞。
- $except
except数组中列出的流将被监视高优先级异常(“带外”)数据的到达。
- $tv_sec
tv_sec和tv_usec共同构成超时参数，tv_sec指定秒数，tv_usec指定微秒数。超时是stream_select()返回前等待时间的上限。
    - tv_sec和tv_usec都被设置为0: tream_select()将不等待数据
    - tv_sec和tv_usec都被设置不为0: 将立即返回，指示流的当前状态
    - 如果tv_sec为NULL, stream_select()可以无限期地阻塞，只在被监视的流中的一个事件发生时返回(或者如果一个信号中断了系统调用)。
- $tv_usec
同上

当stream_select()返回时，将修改读、写和except数组，以指示实际更改了哪些流资源的状态。

返回值:
- 在成功时，stream_select()返回修改后的数组中包含的流资源的数量
- 如果超时在发生任何关注的事情之前过期，则返回0
- 错误时返回FALSE并发出警告(如果系统调用被传入的信号中断，就会发生这种情况)

##### 2.3.3 注意事项
- 检查错误时，请确保使用===操作符。因为stream_select()可能返回0，所以与==的比较将计算为TRUE.
- 不需要将每个数组传递给stream_select()。可以省略它，并使用一个空数组或NULL。
- 使用0的超时值可以让您立即轮询流的状态，但是，在循环中使用0超时值不是一个好主意，因为这会导致脚本消耗太多的CPU时间。
  虽然如果需要同时检查和运行其他代码，使用至少200000微秒的超时值将有助于降低脚本的CPU使用率，但是指定几秒的超时值要好得多。
  记住，超时值是超时的最大时间;一旦请求的流准备好fo, stream_select()将立即返回

##### 2.3.4 示例
这个示例检查数据是否已经到达，以便读取$stream1或$stream2上的数据。由于超时值为0，它将立即返回:
```
<?php
/* Prepare the read array */
$read   = array($stream1, $stream2);
$write  = NULL;
$except = NULL;
if (false === ($num_changed_streams = stream_select($read, $write, $except, 0))) {
    /* Error handling */
} elseif ($num_changed_streams > 0) {
    /* At least on one of the streams something interesting happened */
}
?>
```

#### 2.4 stream_context_create
##### 2.4.1 功能
创建并返回一个资源流上下文，该资源流中包含了 options 中提前设定的所有参数的值。
##### 2.4.2 说明
```
stream_context_create ([ array $options [, array $params ]] ) : resource
```
入参:
- $options
必须是一个二维关联数组，格式如下：$arr['wrapper']['option'] = $value。默认是一个空数组。
- $params
必须是$arr['parameter'] = $value 格式的关联数组。 请参考 context parameters 里的标准资源流参数列表。


##### 2.4.3 示例
```
<?php
$opts = array(
    'http'=>array(
        'method'=>"GET",
        'header'=>"Accept-language: en\r\n" .
            "Cookie: foo=bar\r\n"
    )
);

$context = stream_context_create($opts);

/* Sends an http request to www.example.com
   with additional headers shown above */
$fp = fopen('http://www.example.com', 'r', false, $context);
fpassthru($fp);
fclose($fp);
?>
```
测试:
```
cdyf@jumei:~/tutorial/PHP_Basic/Stream$ php stream_context_create.php
<!doctype html>
<html>
<head>
    <title>Example Domain</title>

    <meta charset="utf-8" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style type="text/css">
    body {
        background-color: #f0f0f2;
        margin: 0;
        padding: 0;
        font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;

    }
    div {
        width: 600px;
        margin: 5em auto;
        padding: 50px;
        background-color: #fff;
        border-radius: 1em;
    }
    a:link, a:visited {
        color: #38488f;
        text-decoration: none;
    }
    @media (max-width: 700px) {
        body {
            background-color: #fff;
        }
        div {
            width: auto;
            margin: 0 auto;
            border-radius: 0;
            padding: 1em;
        }
    }
    </style>
</head>

<body>
<div>
    <h1>Example Domain</h1>
    <p>This domain is established to be used for illustrative examples in documents. You may use this
    domain in examples without prior coordination or asking for permission.</p>
    <p><a href="http://www.iana.org/domains/example">More information...</a></p>
</div>
</body>
</html>
```
#### 2.5 stream_socket_server
##### 2.5.1 功能
创建Internet或Unix域服务器套接字(在指定的local_socket上创建流或数据报套接字。)
这个函数只创建一个套接字，使用stream_socket_accept()开始接受连接。
##### 2.5.2 说明
```
stream_socket_server ( string $local_socket [, int &$errno [, string &$errstr [, int $flags = STREAM_SERVER_BIND | STREAM_SERVER_LISTEN [, resource $context ]]]] ) : resource
```
入参:
- $local_socket
创建套接字的类型由使用标准URL格式指定的传输确定:transport://target。
    - 对于Internet域套接字(AF_INET)，如TCP和UDP, remote_socket参数的目标部分应该由主机名或IP地址、冒号和端口号组成。
    - 对于Unix域套接字，目标部分应该指向文件系统上的套接字文件。
根据环境的不同，Unix域套接字可能不可用。可以使用stream_get_transport()检索可用传输的列表。有关bulitin传输的列表，请参阅受支持的套接字传输列表。
- $errno
如果有可选的errno和errstr参数，则将它们设置为指示在系统级套接字()、bind()和listen()调用中发生的实际系统级错误。如果errno中返回的值为0，函数返回FALSE，则表示bind()调用之前发生了错误。这很可能是由于初始化套接字时出现了问题。
- $errstr
同上
- $flags
位掩码字段，可以设置为套接字创建标志的任何组合。
- $context

注意:
a. errno和errstr参数总是通过引用传递。
b. 对于UDP套接字，必须使用STREAM_SERVER_BIND作为标志参数。

返回值:
返回创建的流，如果错误则返回FALSE。

##### 2.5.3 示例
###### 2.5.3.1 使用TCP服务器套接字
stream_socket_server_tcp.php
```
<?php
$socket = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
if (!$socket) {
    echo "$errstr ($errno)<br />\n";
} else {
    while ($conn = stream_socket_accept($socket)) {
        fwrite($conn, 'The local time is ' . date('n/j/Y g:i a') . "\n");
        echo fgets($conn, 1024);
        fclose($conn);
    }
    fclose($socket);
}
?>
```
测试:
- a. 运行服务端脚本 php stream_socket_server_udp.php
- b. 运行客户端脚本 php stream_socket_client_tcp.php
此时,终端输出
```
cdyf@jumei:~/tutorial/PHP_Basic/Stream$ php stream_socket_client_tcp.php
The local time is 4/17/2019 4:56 pm
```

```
cdyf@jumei:~/tutorial/PHP_Basic/Stream$ php stream_socket_server_tcp.php
GET / HTTP/1.0
```

###### 2.5.3.2 使用UDP服务器套接字
stream_socket_server_udp.php
```
<?php
$socket = stream_socket_server("udp://127.0.0.1:1113", $errno, $errstr, STREAM_SERVER_BIND);
if (!$socket) {
    die("$errstr ($errno)");
}

do {
    $pkt = stream_socket_recvfrom($socket, 1, 0, $peer);
    echo "$peer\n";
    stream_socket_sendto($socket, date("D M j H:i:s Y\r\n"), 0, $peer);
} while ($pkt !== false);

?>
```
测试:
- a. 运行服务端脚本 php stream_socket_server_udp.php
- b. 运行客户端脚本 php stream_socket_client_udp.php
此时终端输出
```
cdyf@jumei:~/tutorial/PHP_Basic/Stream$ php stream_socket_client_udp.php
Wed Apr 17 17:00:49 2019
```

```
cdyf@jumei:~/tutorial/PHP_Basic/Stream$ php stream_socket_server_udp.php
127.0.0.1:55654
```

#### 2.6 stream_socket_client
##### 2.6.1 功能
打开Internet或Unix域套接字连接.
启动到remote_socket指定的目标的流或数据报连接。创建套接字的类型由使用标准URL格式指定的传输确定:transport://target。对于Internet域套接字(AF_INET)，如TCP和UDP, remote_socket参数的目标部分应该由主机名或IP地址、冒号和端口号组成。对于Unix域套接字，目标部分应该指向文件系统上的套接字文件。
##### 2.6.2 说明
```
stream_socket_client ( string $remote_socket [, int &$errno [, string &$errstr [, float $timeout = ini_get("default_socket_timeout") [, int $flags = STREAM_CLIENT_CONNECT [, resource $context ]]]]] ) : resource
```
入参:
- $remote_socket
要连接到的套接字的地址。
- $errno
如果连接失败，将设置为系统级错误号。
- $errstr
如果连接失败，将设置为系统级错误消息。
- $timeout
到connect()系统调用超时的秒数。
    - 此参数仅适用于不进行异步连接尝试时。
    - 要设置在套接字上读取/写入数据的超时，请使用stream_set_timeout()，当前timeout参数只在连接套接字时应用。
- $flags
位掩码字段，可以设置为连接标志的任何组合。目前，连接标志的选择仅限于STREAM_CLIENT_CONNECT(默认)、STREAM_CLIENT_ASYNC_CONNECT和STREAM_CLIENT_PERSISTENT。
- $context
使用stream_context_create()创建的有效上下文资源。

返回值:
成功时返回一个流资源，它可以与其他文件函数(如fgets()、fgetss()、fwrite()、fclose()和feof()一起使用，失败时返回FALSE。

##### 2.6.3 示例
###### 2.6.3.1 使用TCP连接
stream_socket_client_tcp.php
```
<?php
$fp = stream_socket_client("tcp://0.0.0.0:8000", $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    fwrite($fp, "GET / HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n");
    while (!feof($fp)) {
        echo fgets($fp, 1024);
    }
    fclose($fp);
}
?>
```
###### 2.6.3.2 使用UDP连接
stream_socket_client_udp.php
```
<?php
$fp = stream_socket_client("udp://127.0.0.1:1113", $errno, $errstr);
if (!$fp) {
    echo "ERROR: $errno - $errstr<br />\n";
} else {
    fwrite($fp, "\n");
    echo fread($fp, 26);
    fclose($fp);
}
?>
```
测试结果见stream_socket_server示例.

#### 2.7 stream_socket_accept
##### 2.7.1 功能
接受由 stream_socket_server() 创建的套接字连接
##### 2.7.2 说明
```
stream_socket_accept ( resource $server_socket [, float $timeout = ini_get("default_socket_timeout") [, string &$peername ]] ) : resource
```
入参:
- $server_socket
需要接受的服务器创建的套接字连接。
- $timeout
覆盖默认的套接字接受的超时时限。输入的时间需以秒为单位。
- $peername
如果包含该参数并且是可以从选中的传输数据中获取到，则将被设置给连接中的客户端主机的名称（地址）

返回值:
返回接受套接之后的资源流 或者在失败时返回 FALSE。

##### 2.7.3 示例
server端
```
<?php
while (true)
{
// disconnected every 5 seconds...
    receive_message('127.0.0.1','8085',5);
}

function receive_message($ipServer,$portNumber,$nbSecondsIdle)
{
    // creating the socket...
    $socket = stream_socket_server('tcp://'.$ipServer.':'.$portNumber, $errno, $errstr);
    if (!$socket)
    {
        echo "$errstr ($errno)<br />\n";
    }
    else
    {
        // while there is connection, i'll receive it... if I didn't receive a message within $nbSecondsIdle seconds, the following function will stop.
        while ($conn = @stream_socket_accept($socket,$nbSecondsIdle))
        {
            $message= fread($conn, 1024);
            echo 'I have received that : '.$message;
            fputs ($conn, "OK\n");
            fclose ($conn);
        }
        fclose($socket);
    }
}
?>
```
client端
```
<?php

send_message('127.0.0.1','8085','Message to send...');

function send_message($ipServer,$portServer,$message)
{
    $fp = stream_socket_client("tcp://$ipServer:$portServer", $errno, $errstr);
    if (!$fp)
    {
        echo "ERREUR : $errno - $errstr<br />\n";
    }
    else
    {
        fwrite($fp,"$message\n");
        $response =  fread($fp, 4);
        if ($response != "OK\n")
        {echo 'The command couldn\'t be executed...\ncause :'.$response;}
        else
        {echo 'Execution successfull...';}
        fclose($fp);
    }
}
?>
```

测试结果:
- server端
```
cdyf@jumei:~/tutorial/PHP_Basic/Stream$ php stream_socket_accept_server.php
I have received that : Message to send...
```
- client端
```
cdyf@jumei:~/tutorial/PHP_Basic/Stream$ php stream_socket_accept_client.php
Execution successfull...
```

#### 2.8 stream_socket_sendto
##### 2.8.1 功能
向套接字发送消息，不管它是否连接
##### 2.8.2 说明
```
stream_socket_sendto ( resource $socket , string $data [, int $flags = 0 [, string $address ]] ) : int
```
入参:
- $socket
要向其发送数据的套接字。
- $data
要发送的数据.
- $flags
- $address
除非在address中指定了替代地址，否则将使用创建套接字流时指定的地址。
如果指定，它必须是点四(或[ipv6])格式。

返回值:
以整数形式返回结果代码。

##### 2.8.3 示例
```
<?php
/* Open a socket to port 1234 on localhost */
$socket = stream_socket_client('tcp://127.0.0.1:8085');

/* Send ordinary data via ordinary channels. */
fwrite($socket, "Normal data transmit.\n");

/* Send more data out of band. */
stream_socket_sendto($socket, "Out of Band data.\n", STREAM_OOB);

/* Close it up */
fclose($socket);
?>
```
测试:
server端收到信息
```
cdyf@jumei:~/tutorial/PHP_Basic/Stream$ php stream_socket_accept_server.php
I have received that : Normal data transmit.
Out of Band data.
```

#### 2.9 stream_socket_get_name
##### 2.9.1 功能
获取本地或者远程的套接字名称
##### 2.9.2 说明
```
stream_socket_get_name ( resource $handle , bool $want_peer ) : string
```
入参:
- $handle
需要获取其名称的套接字连接。
- $want_peer
如果设置为 TRUE ，那么将返回 remote 套接字连接名称；如果设置为 FALSE 则返回 local 套接字连接名称。

##### 2.9.3 示例
```
<?php
$socket = stream_socket_client('tcp://127.0.0.1:8085');
var_dump(stream_socket_get_name($socket, true));die;
```

测试结果:
```
cdyf@jumei:~/tutorial/PHP_Basic/Stream$ php stream_socket_sendto.php
127.0.0.1:8085
```

#### 2.10 stream_get_meta_data
##### 2.10.1 功能
从封装协议文件指针中取得报头／元数据
##### 2.10.2 说明
```
stream_get_meta_data ( int $fp ) : array
```

返回值:
返回现有 stream 的信息.是一个数组,其包含的数据包括:
- timed_out
如果在上次调用 fread() 或者 fgets() 中等待数据时流超时了则为 TRUE。
- blocked
如果流处于阻塞 IO 模式时为 TRUE。参见 stream_set_blocking()。
- eof
如果流到达文件末尾时为 TRUE。注意对于 socket 流甚至当 unread_bytes 为非零值时也可以为 TRUE。要测定是否有更多数据可读，用 feof() 替代读取本项目的值。
- stream_type
一个描述流底层实现的标注。
- mode
对当前流所要求的访问类型
- unread_bytes
当前在 PHP 自己的内部缓冲区中的字节数。(注:不要在脚本中使用该值)
- seekable
是否可以在当前流中定位。

##### 2.10.3 示例
```
<?php
// creating the socket...
$socket = stream_socket_server('tcp://127.0.0.1:8085', $errno, $errstr);
if (!$socket)
{
    echo "$errstr ($errno)<br />\n";
}
else
{

    var_dump(stream_get_meta_data($socket));
    // while there is connection, i'll receive it... if I didn't receive a message within $nbSecondsIdle seconds, the following function will stop.
    while ($conn = @stream_socket_accept($socket,5))
    {
        $message= fread($conn, 1024);
        echo 'I have received that : '.$message;
        fputs ($conn, "OK\n");
        fclose ($conn);
    }
    fclose($socket);
}
```
测试结果:
```
cdyf@jumei:~/tutorial/PHP_Basic/Stream$ php stream_get_meta_data.php
/home/cdyf/tutorial/PHP_Basic/Stream/stream_get_meta_data.php:11:
array(7) {
  'timed_out' =>
  bool(false)
  'blocked' =>
  bool(true)
  'eof' =>
  bool(false)
  'stream_type' =>
  string(14) "tcp_socket/ssl"
  'mode' =>
  string(2) "r+"
  'unread_bytes' =>
  int(0)
  'seekable' =>
  bool(false)
}
```

























