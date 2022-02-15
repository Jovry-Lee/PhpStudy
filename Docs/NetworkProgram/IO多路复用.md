#### 1 引入

##### 1.1 C10K问题
C10K 就是 Client 10000 问题，即「在同时连接到服务器的客户端数量超过 10000 个的环境中，即便硬件性能足够， 依然无法正常提供服务」，简而言之，就是单机1万个并发连接问题。  
`解决思路`：具体的思路就是通过单个进程或线程服务于多个客户端请求，通过异步编程和事件触发机制替换轮训，IO 采用非阻塞的方式，减少不必要的性能损耗，等等。


#### 2. IO多路复用方案
目前常见的IO多路复用方案有select、poll、epoll、kqueue。

这几个方案的关系大概是这样的：
- select是*NIX出现较早的IO复用方案，有较大缺陷；
- poll是select的升级版，但依然属于新瓶颈；
- epoll是*NIX下终极解决方案，而kqueue则是Mac、BSD下同级别的方案

#### 3. IO多路复用方案-select
在PHP里，操作select的函数叫做`socket_select()`或者`stream_select()`.
##### 3.1 socket_select()
`socket_select ( array|null &$read , array|null &$write , array|null &$except , int|null $seconds , int $microseconds = 0 ) : int|false`
- $read：将想要关注的可读socket保存到read中，但是函数本身又会修改read参数内容，如将四个socket保存到了read中，表示select要监听这四个socket上的可读事件。但是如果只有两个socket上出现了可读，那么select就会将这两个socket保存到read中，也就是read会被从有四个socket修改变成只有两个socket。实际上在PHP里不算是fd，而是一种叫做resource的概念本质上依然是fd。
- $write：关注的write的fd。
- $except：异常的fd
- $seconds：超时时间
- $microseconds：

##### 3.2 select IO的优点？
select目前几乎在所有平台上支持，具有良好的平台支持，

##### 3.2 select IO有哪些问题?
- select方式对单个进程可监控的socket fd数量有限.这个是由UNP或*NIX限制的，由FD_SETSIZE宏确定的，数量为1024.
- select需要将监控的socket从用户态复制到内核态。如调用socket_write向一个socket fd写数据，那么需要将数据先从用户态复制到内核以及socket缓冲区，最后调用完成后还需要回到用户态，其中涉及了两次场景切换与一次复制。
- 每次都需要遍历所有的socket来查询具体是哪一个socket具备可读或可写条件。  
（注：对于php的socket_select方法，是将底层的select调用封装过的，通过每个socket的FD_ISSET宏判断是否可读/可写，这个遍历的时间复杂度是O(n)）

示例：通过socket_select实现一个简单的长连接聊天室。
```chatRoom.php
<?php
$host = '0.0.0.0';
$port = 6666;
$listen_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_set_option($listen_socket, SOL_SOCKET, SO_REUSEADDR, 1); // 将SO_REUSEADDR设置为1，这样这个地址就可以被反复使用了，否则可能会遇到错误提示：address already in use问题
socket_set_option($listen_socket, SOL_SOCKET, SO_REUSEPORT, 1); // 将SO_REUSEPORT设置为1，可以重复利用某个端口

socket_bind($listen_socket, $host, $port);
socket_listen($listen_socket);

// 将listen-socket设置为非阻塞
socket_set_nonblock($listen_socket);

socket_getsockname( $listen_socket, $addr, $port );
echo 'Chatroom - ' . $addr . ':' . $port . PHP_EOL;
// 将listen-socket加入到client数组中
$client = array($listen_socket);
while (true) {
    // select会修改read中，所以client相当于是一种备份。client中保存的就是你所有想要监听的socket，但是并不是每个socket都会有可读发生，但是select会修改read数组，将可读的socket保存到read中，下次循环重新开始的时候，read需要从client中再次将所有需要监听可读的socket全部
    $read = $client;
    $write = array();
    $exception = array();

    // 系统会被阻塞在socket_select()上一直到有可读、可写等事件发生的时候调用才会返回，并同时将可读、可写等数据自动保存到read、write等数组中，ret返回结果是可读可写等数量。
    // 对于listen-socket而言，select会调用方监控发生在listen-socket上的可读事件，即有新客户端连接连接上来了。
    $ret = socket_select($read, $write, $exception, null);
    //print_r( $read );
    //echo "select-loop : {$ret}".PHP_EOL.PHP_EOL.PHP_EOL;
    if ($ret <= 0) {
        continue;
    }
    // 就是说，如果 listen-socket 中有事件，listen-socket能有啥事件：就是用新的客户端来了.
    if (in_array($listen_socket, $read)) {
        $connection_socket = socket_accept($listen_socket);
        if (!$connection_socket) {
            continue;
        }
        socket_getpeername($connection_socket, $client_ip, $client_port);
        echo "Client {$client_ip}:{$client_port}" . PHP_EOL;
        // 将新连接上来的客户端的socket保存到client中
        $client[] = $connection_socket;
        // 将listen-socket从read中手工移除掉，因为后面要开始从connection-socket中读取数据了，listen-socket上只能做accept操作不能做read操作
        $key = array_search($listen_socket, $read);
        unset($read[$key]);
    }
    // 对于其他的connection-socket
    foreach ($read as $read_key => $read_fd) {
        // 读取数据
        socket_recv($read_fd, $recv_content, 1024, 0);
        if (!$recv_content) {
            echo "客户端 {$read_fd} 丢失" . PHP_EOL;
            unset($client[$read_key]);
            socket_close($read_fd);
            continue;
        }
        $recv_content = "{$read_fd}说：" . $recv_content;
        // 将收到的消息广播给除了自己以外的其他所有在线客户端，其实也就是除了自己fd之外的其他所有fd
        foreach ($client as $fd_item) {
            if ($fd_item == $listen_socket) {
                continue;
            }
            if ($fd_item != $read_fd) {
                echo "发送给{$read_fd}" . PHP_EOL;
                socket_write($fd_item, $recv_content, strlen($recv_content));
            }
        }

        // 聊天室是需要长连接的，不能断开
        //unset( $client[ $read_key ] );
        //socket_shutdown( $read_fd );
        //socket_close( $read_fd );
    }
}
```
测试：
```Server端
% php chatRoom.php 
Chatroom - 0.0.0.0:6666
Client 127.0.0.1:51451
Client 127.0.0.1:51480
发送给Resource id #6
发送给Resource id #5
发送给Resource id #6
```

```Client1
telnet 127.0.0.1 6666
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
Hi~
Resource id #6说：Hello
Hi~
Resource id #6说：What are you doing?
```

```Client2
 % telnet 127.0.0.1 6666
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
Hello
Resource id #5说：Hi~
What are you doing?
```

示例：实现一个简单的HTTP协议
```httpServer.php
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
        var_dump( $ret );
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
```

```Http.php
<?php

class Http
{
    // 定义下目前支持的http方法们，目前只支持get和post
    private static $a_method = array('get', 'post');

    public static function decode($s_raw_http_content)
    {
        $s_http_method = '';
        $s_http_version = '';
        $s_http_pathinfo = '';
        $s_http_querystring = '';
        $s_http_body_boundry = '';  // 当post方法且为form-data的时候.
        $a_http_post = array();
        $a_http_get = array();
        $a_http_header = array();
        $a_http_file = array();
        // 先通过两个 \r\n\r\n 把 请求行+请求头 与 请求体 分割开来.
        list($s_http_line_and_header, $s_http_body) = explode("\r\n\r\n", $s_raw_http_content, 2);
        // 再分解$s_http_line_and_header数组
        // 数组的第一个元素一定是 请求行
        // 数组剩余所有元素就是 请求头
        $a_http_line_header = explode("\r\n", $s_http_line_and_header);
        $s_http_line = $a_http_line_header[0];
        unset($a_http_line_header[0]);
        $a_http_raw_header = $a_http_line_header;
        // 好了，请求行 + 请求头数组 + 请求体 都有了
        // 先从请求行分解 method + pathinfo + querystring + http版本
        list($s_http_method, $s_http_pathinfo_querystring, $s_http_version) = explode(' ', $s_http_line);
        if (false === strpos($s_http_pathinfo_querystring, "?")) {
            $s_http_pathinfo = $s_http_pathinfo_querystring;
        } else {
            list($s_http_pathinfo, $s_http_querystring) = explode('?', $s_http_pathinfo_querystring);
        }
        // 处理querystring为数组
        if ('' != $s_http_querystring) {
            $a_raw_http_get = explode('&', $s_http_querystring);
            foreach ($a_raw_http_get as $s_http_get_item) {
                if ('' != trim($s_http_get_item)) {
                    list($s_get_key, $s_get_value) = explode('=', $s_http_get_item);
                    $a_http_get[$s_get_key] = $s_get_value;
                }
            }
        }
        // 处理$s_http_header
        foreach ($a_http_raw_header as $a_raw_http_header_key => $a_raw_http_header_item) {
            if ('' != trim($a_raw_http_header_item)) {
                list($s_http_header_key, $s_http_header_value) = explode(":", $a_raw_http_header_item);
                $a_http_header[strtoupper($s_http_header_key)] = $s_http_header_value;
            }
        }

        // 如果是post方法，处理post body
        if ('post' === strtolower($s_http_method)) {
            // post 方法里要关注几种不同的content-type
            // x-www-form-urlencoded
            if ('application/x-www-form-urlencoded' == trim($a_http_header['CONTENT-TYPE'])) {
                $a_http_raw_post = explode("&", $s_http_body);
                // 解析http body
                foreach ($a_http_raw_post as $s_http_raw_body_item) {
                    if ('' != $s_http_raw_body_item) {
                        list($s_http_raw_body_key, $s_http_raw_body_value) = explode("=", $s_http_raw_body_item);
                        $a_http_post[$s_http_raw_body_key] = $s_http_raw_body_value;
                    }
                }
            }
            // form-data
            if (false !== strpos($a_http_header['CONTENT-TYPE'], 'multipart/form-data')) {
                list($s_http_header_content_type, $s_http_body_raw_boundry) = explode(';', $a_http_header['CONTENT-TYPE']);
                $a_http_header['CONTENT-TYPE'] = trim($s_http_header_content_type);
                list($_temp_unused, $s_http_body_boundry) = explode('=', $s_http_body_raw_boundry);
                $s_http_body_boundry = '--' . $s_http_body_boundry;
                $a_http_raw_post = explode($s_http_body_boundry . "\r\n", $s_http_body);
                foreach ($a_http_raw_post as $s_http_raw_body_item) {
                    if ('' != trim($s_http_raw_body_item)) {
                        echo $s_http_raw_body_item;
                        //$a_http_raw_body_item = explode( ';', $s_http_raw_body_item );
                    }
                }
            }
        }

        // 整理数据
        $a_ret = array(
            'method' => $s_http_method,
            'version' => $s_http_version,
            'pathinfo' => $s_http_pathinfo,
            'post' => $a_http_post,
            'get' => $a_http_get,
            'header' => $a_http_header,
        );
        return $a_ret;
    }


    public static function encode($a_data)
    {
        $s_data = json_encode($a_data);
        $s_http_line = "HTTP/1.1 200 OK";
        $a_http_header = array(
            "Date" => gmdate("M d Y H:i:s", time()),
            "Content-Type" => "application/json",
            "Content-Length" => strlen($s_data),
        );
        $s_http_header = '';
        foreach ($a_http_header as $s_http_header_key => $s_http_header_item) {
            $_s_header_line = $s_http_header_key . ': ' . $s_http_header_item;
            $s_http_header = $s_http_header . $_s_header_line . "\r\n";
        }
        $s_ret = $s_http_line . "\r\n" . $s_http_header . "\r\n" . $s_data;
        return $s_ret;
    }
}
```
测试：
```server端-模拟get请求。
seven@SevendeMacBook-Pro SocketProgram % php httpServer.php
Select HTTP Server - 0.0.0.0:6666
select-loop : 1


Array
(
    [0] => Resource id #5
)
开始处理客户端连接~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Client 127.0.0.1:56779
结束处理客户端连接~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select-loop : 1


Array
(
    [1] => Resource id #6
)
开始处理数据传输~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
int(124)
GET /user/login.php?username=wahaha&password=123456 HTTP/1.1
Host: 127.0.0.1:6666
User-Agent: curl/7.64.1
Accept: */*

Array
(
    [method] => GET
    [version] => HTTP/1.1
    [pathinfo] => /user/login.php
    [post] => Array
        (
        )

    [get] => Array
        (
            [username] => wahaha
            [password] => 123456
        )

    [header] => Array
        (
            [HOST] =>  127.0.0.1
            [USER-AGENT] =>  curl/7.64.1
            [ACCEPT] =>  */*
        )

)
结束处理数据传输~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
```

```client端模拟get请求
seven@SevendeMacBook-Pro SocketProgram % curl -X GET "http://127.0.0.1:6666/user/login.php?username=wahaha&password=123456"
{"username":"wahaha"}%
```

```server端-模拟Post请求
seven@SevendeMacBook-Pro SocketProgram % php httpServer.php
select-loop : 1


Array
(
    [0] => Resource id #5
)
开始处理客户端连接~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Client 127.0.0.1:57709
结束处理客户端连接~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select-loop : 1


Array
(
    [2] => Resource id #7
)
开始处理数据传输~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
int(300)
POST / HTTP/1.1
User-Agent: PostmanRuntime/7.26.8
Accept: */*
Postman-Token: 3a23b40f-3b7f-4b50-9787-7cfc3b645659
Host: 127.0.0.1:6666
Accept-Encoding: gzip, deflate, br
Connection: keep-alive
Content-Type: application/x-www-form-urlencoded
Content-Length: 29

username=etcd&password=123456Array
(
    [method] => POST
    [version] => HTTP/1.1
    [pathinfo] => /
    [post] => Array
        (
            [username] => etcd
            [password] => 123456
        )

    [get] => Array
        (
        )

    [header] => Array
        (
            [USER-AGENT] =>  PostmanRuntime/7.26.8
            [ACCEPT] =>  */*
            [POSTMAN-TOKEN] =>  3a23b40f-3b7f-4b50-9787-7cfc3b645659
            [HOST] =>  127.0.0.1
            [ACCEPT-ENCODING] =>  gzip, deflate, br
            [CONNECTION] =>  keep-alive
            [CONTENT-TYPE] =>  application/x-www-form-urlencoded
            [CONTENT-LENGTH] =>  29
        )

)
结束处理数据传输~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
```
```client端通过Postman模拟post请求.
# HTTP原文：
POST / HTTP/1.1
Host: 127.0.0.1:6666
Content-Type: application/x-www-form-urlencoded
Content-Length: 29

username=etcd&password=123456

# CURL请求：
curl --location --request POST 'http://127.0.0.1:6666' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'username=etcd' \
--data-urlencode 'password=123456'
```

#### 4. IO多路复用方案-poll
针对select的三个问题，*NIX中实现了一个poll IO，但poll也仅解决了1024的问题。
epoll事先通过epoll_ctl()来注册一个文件描述符，一旦基于某个文件描述符就绪时，内核会采用类似callback的回调机制，迅速激活这个文件描述符，当进程调用epoll_wait()时便得到通知。  
（注：此处去标了遍历文件描述符，而是通过监听回调的机制）

#### 5. IO多路复用方案-epoll
##### 5.1 介绍
epoll完美的解决了select中的三个问题。  
在PHP中，并不能找到直接操作epoll的函数方法，但可以通过LibEvent（Libevent是一个事件库，它对目前市面上的各种IO复用技术进行了统一的封装，且它是跨平台的）进行处理，要使用LibEvent需安装event扩展。

epoll操作文件符的两种模式：
- LT（Level Trigger）：此方式下，若监听了有X个事件发生，那么内核态会将这些事件拷贝到用户态，但若用户只处理了其中一件，剩下的X-1件未处理，那么下次处理时，未处理完的X-1个事件依旧会从内核态拷贝到用户态。
    - 优点：事件不会丢失；
    - 缺点：浪费资源；
- ET（Edge Trigger）：此方式下，若发生了X个事件，然而只处理了一个，那么剩余的X-1个事件将不再进行处理。
    - 优点：性能较高；
    - 缺点：事件可能丢失。 


在Event中，最重要的三个类：
- Event：具体的事件。例如来了一个连接，就需要给这个连接初始化一个Event并标记上可读；
    - 构造函数：`public __construct ( EventBase $base , mixed $fd , int $what , callable $cb , mixed $arg = NULL )`
        - $base：就是EventBase；
        - $fd：PHP中的stream、socket fd等，
            - 若为event为信号类型那么就是信号名比如SIGHUP、SIGSTOP
            - 若为时间类型为则为-1；
        - $what：操作：
            - Event::READ：读；
                - 触发条件：只要网络缓冲中还有数据，回调函数就会被触发
            - Event::WRITE：写；（思考：在Event::READ回调中读取了客户端飞过来的数据后，马上使用socket_write()等把数据再飞回去给客户端，为什么还需要有Event::WRITE事件呢？）
                - 触发条件：只要塞给网络缓冲的数据被写完，回调函数就会被触发
                - 常规使用方法：当Event::READ事件发生后，在回调函数中首先读取数据，然后准备一个发送数据的自定义缓冲区，当这个发送数据的自定义缓冲区（注：不是socket缓冲区）中没有数据后，在客户端socket上搞一发写事件并挂起（执行add
                ()方法），然后当Event::WRITE事件发生后开始执行写回调，在写回调里完成逻辑后，将该写事件del掉即可。
            - Event::SIGNAL：信号类型事件；
            - Event::TIMEOUT：时间事件；
            - Event::PERSIST：表示是否持续执行，否则将只执行一次。通常与Event::READ和Event::WRITE事件组合使用。
        - $cb：回调函数；
            - Event回调原型为：`callback ( mixed $fd = null , int $what = ? , mixed $arg = null ) : void`
                - $fd：与事件关联的文件描述符、流资源或套接字。对于信号事件，fd等于信号号；
                - $what：所有触发事件的位掩码；
                - $arg：用户自定义参数。
            - Event::timer()回调原型：`callback ( mixed $arg = null ) : void`
            - Event::signal() 回调原型：`callback ( int $signum = ? , mixed $arg = null ) : void`
                - $signum：触发信号的编号(例如:SIGTERM)。
        - $arg：回调函数参数。
    - add函数（将事件挂起准备执行）：`public Event::add ( float $timeout = ? ) : bool`。
        - $timeout：超时时间；
    - del函数（与add函数相反，使事件非挂起）：`public Event::del ( ) : bool`
    - Event::timer函数：`public static Event::timer ( EventBase $base , callable $cb , mixed $arg = ? ) : Event`
    - addTimer函数：`public Event::addTimer ( float $timeout = ? ) : bool`
- EventBase：事件基础。所有的Event都是在EventBase上运行的；
    - 构造函数：`public __construct ( EventConfig $cfg = ? )`；
    - loop函数（调度等待事件）：`public EventBase::loop ( int $flags = ? ) : bool`
- EventConfig：配置类，可通过对该类进行传参控制EventBase;
    - 构造函数：
    - avoidMethod函数（避免使用的方法）：`public EventConfig::avoidMethod ( string $method ) : bool`
    - requireFeatures（添加方法特征）：`public EventConfig::requireFeatures ( int $feature ) : bool`
        - $feature参数-EventConfig::FEATURE_ET：如果要开启这个选项，那么选用的IO复用方式一定要支持ET;
        - $feature参数-EventConfig::FEATURE_O1：选用的IO复用方法必须支持O(1)级别的发现可读/可写的事件;
        - $feature参数-EventConfig::FEATURE_FDS：选用的IO复用发放不光能支持socket，还能支持其他文件类型的文件描述符。
        （注：对于Linux系统，EventConfig::FEATURE_ET和EventConfig::FEATURE_O1如果被打开，那么IO复用将会采用epoll；然而epoll不支持普通文件，所以当EventConfig::FEATURE_FDS
        被开启后，O1和ET特性将会被关闭，此时在Linux下poll IO复用是支持普通文件的。）

其次是：
- EventBuffer
- EventBufferEvent两个类。

==php中查看当前系统支持的IO复用方法，及默认使用的IO复用方法==
```
// 查看当前支持的IO复用方法
echo "System support methods：\n";
print_r( Event::getSupportedMethods() );

// 查看默认情况下Libevent使用哪个IO复用
$o_event_base = new EventBase();
echo "default IO method：" . $o_event_base->getMethod().PHP_EOL;
```
测试：（运行系统：MacOS系统）
```
seven@SevendeMacBook-Pro Timer % php LibEventTest_4.php
System support methods：
Array
(
    [0] => kqueue
    [1] => poll
    [2] => select
)
default IO method：kqueue
```

##### 5.2 示例
###### 5.2.1 通过LibEvent实现一个毫秒级的定时器。
```libeventTest_1.php
<?php
// 初始化一个空的EventConfig，用这个空的EventConfig初始化一个EventBase
$o_event_config = new EventConfig();
$o_event_base = new EventBase($o_event_config);
// 初始化一个 timer类型的Event
$o_timer_event = new Event($o_event_base, -1, Event::TIMEOUT | Event::PERSIST, function () {
    echo "bingo" . PHP_EOL;
});

// 设置一个超时时间，将事件挂起准备执行
$o_timer_event->add(0.7);
// 让event_base loop起来，相当于while（true）
$o_event_base->loop();
```
测试：
```
seven@SevendeMacBook-Pro Timer % php LibeventTest_1.php
bingo
bingo
bingo
...
```

###### 5.2.2 通过LibEvent实现程序中动态控制事件。
```libeventTest_2.php
<?php

$oEventConfig = new EventConfig();
$oEventBase = new EventBase($oEventConfig);
$iDiy = time();
// public __construct ( EventBase $base , mixed $fd , int $what , callable $cb , mixed $arg = NULL )
$oTimerEvent = new Event($oEventBase, -1, Event::TIMEOUT | Event::PERSIST, function ($iFd, $mWhat, $iDiy) use (&$oTimerEvent) {
    echo "自定义参数：" . $iDiy . "\n";
    $iCounter = mt_rand(1, 3);
    if ($iCounter == 2) {
        var_dump($oTimerEvent->del());
    }
}, $iDiy);

$oTimerEvent->add(0.5);
$oEventBase->loop();
```
测试：
```
seven@SevendeMacBook-Pro Timer % php LibeventTest_2.php
自定义参数：1622888600
自定义参数：1622888600
自定义参数：1622888600
自定义参数：1622888600
bool(true)
```

###### 5.2.3 通过Event::timer()方法实现定时器。
```LibeventTest_3_1.php
<?php
$timeout = 2;
$o_event_config = new EventConfig();
$o_event_base = new EventBase($o_event_config);
$event_timer = Event::timer($o_event_base, function ($timeout) use (&$event_timer) {
    echo "$timeout seconds elapsed\n";
    // $event_timer->del(); // 只执行一次
    $event_timer->addTimer($timeout); // 将定时触发
}, $timeout);
$event_timer->addTimer($timeout);
$o_event_base->loop();
```
测试：
```
seven@SevendeMacBook-Pro Timer % php LibeventTest_3.php
2 seconds elapsed
```

###### 5.2.4 通过Libevent实现信号事件。
```LibEventTest_3.php
<?php
echo getmypid() . PHP_EOL;
$o_event_config = new EventConfig();
$o_event_base = new EventBase( $o_event_config );
$o_timer_event = new Event( $o_event_base, SIGTERM, Event::SIGNAL | Event::PERSIST, function() {
    echo "sigterm".PHP_EOL;
} );
$o_timer_event->add();
$o_event_base->loop();
```

测试：
```终端1
seven@SevendeMacBook-Pro Timer % php LibEventTest_3.php
45040
sigterm

```
```终端2：发送信号
seven@SevendeMacBook-Pro Workerman % kill -SIGTERM 45040
```

###### 5.2.5 查看/设置IO复用方法
```LibEventTest_4.php
<?php
// 查看当前支持的IO复用方法
echo "System support methods：\n";
print_r( Event::getSupportedMethods() );

// 查看默认情况下Libevent使用哪个IO复用
$o_event_base = new EventBase();
echo "default IO method：" . $o_event_base->getMethod().PHP_EOL;

// 某些情况下我们就只需要指定使用poll
$o_event_config = new EventConfig();
$o_event_config->avoidMethod( "select" );
$o_event_config->avoidMethod( "epoll" );
$o_event_base = new EventBase( $o_event_config );
echo $o_event_base->getMethod().PHP_EOL;
$o_event_base->loop();
```
测试：
```
seven@SevendeMacBook-Pro Timer % php LibEventTest_4.php
System support methods：
Array
(
    [0] => kqueue
    [1] => poll
    [2] => select
)
default IO method：kqueue
kqueue
```

###### 5.2.6 配置事件特性
```LibEventTest_5.php
<?php
$o_event_config = new EventConfig();
// 通过requireFeatures方法来配置控制
$o_event_config->requireFeatures( EventConfig::FEATURE_ET );
//$o_event_config->requireFeatures( EventConfig::FEATURE_O1 );
//$o_event_config->requireFeatures( EventConfig::FEATURE_FDS );
$o_event_base = new EventBase( $o_event_config );
// 通过getFeatures获取当前事件base的具体特性
$i_features = $o_event_base->getFeatures();
// 通过&方法，也就是与方法来判断选项是否开启
( $i_features & EventConfig::FEATURE_ET ) and print("ET - edge-triggered IO\n");
( $i_features & EventConfig::FEATURE_O1 ) and print("O1 - O(1) operation for adding/deletting events\n");
( $i_features & EventConfig::FEATURE_FDS ) and print("FDS - arbitrary file descriptor types, and not just sockets\n");
$o_event_base->loop();
```
测试：MAC系统下默认使用Kqueue IO复用，可以同时支持这三种特性。
```
seven@SevendeMacBook-Pro Timer % php LibEventTest_5.php
ET - edge-triggered IO
O1 - O(1) operation for adding/deletting events
FDS - arbitrary file descriptor types, and not just sockets
```

###### 5.2.7 epoll深入示例，通过event扩展实现最基础的网络IO

流程：
- 第一步：创建好一个非阻塞的listen socket；
- 第二步：在listen socket上创建一个持久的读事件；
- 第三步：当listen socket发现可读事件后就执行socket_accept操作；
- 第四步：为客户端连接socket添加持久读取事件；
- 第五步：从客户端socket读取数据；  
（==注：实现过程中，需要将客户端连接/事件保存到数组中，否则客户端将在建立连接后立即断开/事件丢失，读取不到==）

```LibEventBaseIO.php
<?php
// 第一步：创建好一个非阻塞的listen socket。
$host = '0.0.0.0';
$port = '6666';
$listenSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($listenSocket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_option($listenSocket, SOL_SOCKET, SO_REUSEPORT, 1);
socket_bind($listenSocket, $host, $port);
socket_listen($listenSocket);
// 设置非阻塞。
socket_set_nonblock($listenSocket);

// 第二步：在listen socket上创建一个持久的读事件。
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
        // 第三步：当listen socket发现可读事件后就执行socket_accept操作。
        $connectSocket = socket_accept($fd);
        $clientArr[] = $connectSocket;
        echo "客户端建立连接：{$connectSocket}" . PHP_EOL;

        // 第四步：为客户端连接socket添加持久读取事件
        $event = new Event(
            $evenBase,
            $connectSocket,
            Event::READ | Event::PERSIST,
            function ($fd) {
                // 第五步：从客户端socket读取数据。
                $content = socket_read($fd, 1024);
                echo $content;
            }
        );
        $event->add();
        $eventArr[] = $event;
    }, $evenBase);

$event->add();
$eventArr[] = $event;
$evenBase->loop();
```
测试：
```server端
seven@SevendeMacBook-Pro LibEvent % php LibEventBaseIO.php
methodName：kqueue
客户端建立连接：Resource id #5
Hello,Seven!
```
```client端
seven@SevendeMacBook-Pro Workerman % telnet 127.0.0.1 6666
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
Hello,Seven!
```

###### 5.2.8 epoll深入示例，通过LibEvent实现一个聊天室
```LibEventChatRoom.php
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
```
测试：
```Sever端
seven@SevendeMacBook-Pro LibEvent % php LibEventChatRoom.php
methodName：kqueue
客户端建立连接：Resource id #5
客户端建立连接：Resource id #6
Resource id #5发送消息：Hi,I'm Seven!

Resource id #5开始群发消息
```
```Client端1
seven@SevendeMacBook-Pro Workerman % telnet 127.0.0.1 6666
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
Hi,I'm Seven!
```
```Client端2
seven@SevendeMacBook-Pro Workerman % telnet 127.0.0.1 6666
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
Hi,I'm Seven!
```

如果没有大量的idle -connection或者dead-connection，epoll的效率并不会比select/poll高很多，但是当遇到大量的idle- connection，就会发现epoll的效率大大高于select/poll。

参考资料：
[Linux IO模式及 select、poll、epoll详解](https://segmentfault.com/a/1190000003063859)