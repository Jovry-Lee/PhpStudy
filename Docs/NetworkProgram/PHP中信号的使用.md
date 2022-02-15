#### 1 简介
PHP主要提供了大概四五个左右的函数来供我们使用信号。

使用信号只需要两步：
- ①、给进程安装信号处理函数：`signal_handler`；
- ②、向进程分发信号：`pcntl_signal_dispatch` / `pcntl_async_signals`；

**未决状态**：信号从产生到进程处理间的这个时间差过程，称之为信号是「未决」的。
进程处理`未决状态`的信号的方式：
- ①、忽略该信号；
- ②、使用默认方式处理；
- ③、自定义方式处理；
- ④、暂时「兜住」（阻塞）该信号，然后后续再进行处理；

#### 2 信号的分发
##### 2.1 使用pcntl_signal_dispatch()分发信号
pcntl_signal_dispatch()函数**不能实现真正的异步信号通知**，因为TA的用法就是[不断地分发]，就是说TA并不是说分发一次就能一直收到信号，而是说上次信号响应过后如果你还想让捕捉[还尚未到来的]下次信号，那就一定要再次dispatch！

示例：
```Singal_1.php 
<?php
// 信号处理器...
function signal_handler( $i_signo ) {
    switch ( $i_signo ) {
        case SIGTERM:
            echo "sigterm信号.".PHP_EOL;
            break;
        case SIGUSR2:
            echo "sigusr2信号.".PHP_EOL;
            break;
        case SIGUSR1:
            echo "sigusr1信号.".PHP_EOL;
            break;
        default:
            echo "未知信号.".PHP_EOL;
    }
}

// 给进程安装信号...
pcntl_signal( SIGTERM, "signal_handler" );
pcntl_signal( SIGUSR1, "signal_handler" );
pcntl_signal( SIGUSR2, "signal_handler" );

// while保持进程不要退出..
while ( true ) {
    pcntl_signal_dispatch();
    sleep( 10);
    // 使用了两次pcntl_signal_dispatch。
    pcntl_signal_dispatch();
}
```

运行该脚本，通过另一终端使用`kill`命令发送信号，如下所示：
```
终端2：
seven@SevendeMacBook-Pro PhpNetworkProgram % kill -SIGTERM 20964
seven@SevendeMacBook-Pro PhpNetworkProgram % kill -SIGUSR2 20964
seven@SevendeMacBook-Pro PhpNetworkProgram % kill -SIGUSR1 20964

终端1：
seven@SevendeMacBook-Pro PhpNetworkProgram % php Singal_1.php 
sigterm信号.
sigusr2信号.
sigusr1信号.
^C
```

==Workerman源码中，pcntl_signal_dispatch函数调用了两次，为什么呢？==  
参考资料②中，亮哥亲自解答的：pcntl_signal_dispatch()是处理已经收到的信号，在信号处理过程中，这时候有新的信号到来，新的信号放入信号队列不做处理(因为需要再次调用pcntl_signal_dispatch()才能被处理)。
如下示例可复现信号丢失/延迟的情况：
```SingalLost.php
<?php
echo "pid=".posix_getpid()."\n";
pcntl_signal(SIGUSR1, function(){
    echo "GET SIGNAL\n";
    posix_kill(posix_getpid(), SIGUSR1); // 这里模拟在处理信号的过程中收到新的信号
}, false);
while (1) {
    pcntl_signal_dispatch();
    sleep(10);
    //pcntl_signal_dispatch();
}
```

运行`SingalLost.php`，然后通过另一个终端发送`SIGUSR1`信号，若无第二个`pcntl_signal_dispatch`，此时只会打印出一个`GET SIGNAL`（准确来说，延迟到第二个`pcntl_signal_dispatch
`执行后，会再打印出出一个`GET SIGNAL`，若无第二个`pcntl_signal_dispatch`则该信号将被丢失）

测试：
```
case1：未调用第二个pcntl_signal_dispatch的情况，会出现
seven@SevendeMacBook-Pro PhpNetworkProgram % php SingalLost_1.php 
pid=21251
GET SIGNAL
# 等待10s后
GET SIGNAL
^C

case2：调用了第二个pcntl_signal_dispatch的情况；
seven@SevendeMacBook-Pro PhpNetworkProgram % php SingalLost_1.php
pid=21277
GET SIGNAL
GET SIGNAL
^C
```

##### 2.2 使用pcntl_async_signals()分发信号
pcntl_signal_dispatch()不能实现真正的异步信号通知，PHP7.1版本后支持了`pcntl_async_signal`函数，该函数相比`pcntl_signal_dispatch`能更好的实现信号的异步捕获。

示例：
```
<?php
pcntl_async_signals(true);
// 信号处理器...
function signal_handler( $i_signo ) {
    switch ( $i_signo ) {
        case SIGTERM:
            echo "sigterm信号.".PHP_EOL;
            break;
        case SIGUSR2:
            echo "sigusr2信号.".PHP_EOL;
            break;
        case SIGUSR1:
            echo "sigusr1信号.".PHP_EOL;
            break;
        default:
            echo "未知信号.".PHP_EOL;
    }
}
// 给进程安装信号...
pcntl_signal( SIGTERM, "signal_handler" );
pcntl_signal( SIGUSR1, "signal_handler" );
pcntl_signal( SIGUSR2, "signal_handler" );
// while保持进程不要退出..
while ( true ) {
    sleep( 1 );
}
```

运行该脚本，通过另一终端使用`kill`命令发送信号，如下所示：
```
终端2：
seven@SevendeMacBook-Pro PhpNetworkProgram % kill -SIGUSR1 21913          
seven@SevendeMacBook-Pro PhpNetworkProgram % kill -SIGUSR2 21913
seven@SevendeMacBook-Pro PhpNetworkProgram % kill -SIGTERM 21913

终端1：
seven@SevendeMacBook-Pro PhpNetworkProgram % php AsyncSignal_1.php 
sigusr1信号.
sigusr2信号.
sigterm信号.
^C
```

对于pcntl_signal_dispatch极端示例中，信号处理过程中发送信号的情况，pcntl_async_signal处理如下：
```
<?php

pcntl_async_signals( true );

echo "pid=".posix_getpid()."\n";
pcntl_signal(SIGUSR1, function(){
    echo "GET SIGNAL\n";
    posix_kill(posix_getpid(), SIGUSR1); // 这里模拟在处理信号的过程中收到新的信号
}, false);
while (1) {
    sleep(10);
}
```

运行结果：相比pcntl_signal_dispatch函数，pcntl_async_signal能触发四层嵌套。
```
seven@SevendeMacBook-Pro PhpNetworkProgram % php SingalLost_2.php 
pid=21968
GET SIGNAL
GET SIGNAL
GET SIGNAL
GET SIGNAL
# 10s后
GET SIGNAL
GET SIGNAL
GET SIGNAL
GET SIGNAL
...
```

#### 3 阻塞/移除阻塞指定信号
**阻塞信号的应用场景**：
比如说reload热加载功能，就可以尝试利用信号阻塞来实现。大概的流程就是：
- ①、主进程收到reload动作后，就由主进程来向子进程发送SIGTERM信号；
- ②、子进程收到SIGTERM信号后先将信号阻塞，当所有业务逻辑（一般都是socket事件循环）完成后再响应SIGTERM信号，这样就可以保证[先处理完成当前进程内的业务，然后再退出]，
- ③、而后主进程再去重新拉起新的子进程。


##### 3.1 使用pcntl_sigprocmask()进行信号的阻塞/移除阻塞
`pcntl_sigprocmask ( int $how , array $set , array &$oldset = ? ) : bool`函数可用于实现信号的阻塞/移除阻塞的功能，其中how参数用于设置该函数的行为：
- $how参数:
    - SIG_BLOCK: 将信号添加到当前阻塞的信号中；
    - SIG_UNBLOCK: 从当前阻塞的信号中移除信号.
    - SIG_SETMASK: 用给定的信号列表替换当前阻塞的信号.

示例：
```
<?php
pcntl_async_signals( true );
// 信号处理器...
function signal_handler( $i_signo ) {
    switch ( $i_signo ) {
        case SIGUSR2:
            echo "sigusr2信号.".PHP_EOL;
            break;
        case SIGUSR1:
            echo "sigusr1信号.".PHP_EOL;
            break;
        default:
            echo "未知信号.".PHP_EOL;
    }
}
// 给进程安装信号...
pcntl_signal( SIGUSR1, "signal_handler" );
pcntl_signal( SIGUSR2, "signal_handler" );
// 阻塞SIGUSR1信号。
pcntl_sigprocmask( SIG_BLOCK, array( SIGUSR1 ), $a_oldset );
// while保持进程不要退出..
$i_counter = 0;
echo getmypid() . "\n";

while ( true ) {
    $i_counter++;
    sleep( 1 );
    echo $i_counter.PHP_EOL;
    if ( 20 == $i_counter ) {
        // 移除SIGUSR1信号阻塞。
        pcntl_sigprocmask( SIG_UNBLOCK, array( SIGUSR1 ), $a_oldset );
    }
}
```

运行结果：可见处理进程实际上是先将`SIGUSR1`信号兜住，然后再进行响应。
```
终端1：
seven@SevendeMacBook-Pro PhpNetworkProgram % kill -SIGUSR1 2328

终端2：
seven@SevendeMacBook-Pro PhpNetworkProgram % php AsynSignalProcMask.php
2328
1
2
3
...
19
20
sigusr1信号.
```

##### 3.2 使用pcntl_sigwaitinfo()检测阻塞信号
当使用pcntl_sigprocmask()设置了SIGTERM阻塞后，可以利用pcntl_sigwaitinfo()的函数来检验这种阻塞(注：这个函数本身也是阻塞的)
示例：
```
<?php
pcntl_sigprocmask(SIG_BLOCK, array(SIGTERM));
$info = array();
$ret = pcntl_sigwaitinfo(array(SIGTERM), $info);
var_dump( $ret );
var_dump( $info );
```
执行中报错：MacOS中不支持该方法(参考资料③)
```
PHP Fatal error:  Uncaught Error: Call to undefined function pcntl_sigwaitinfo()
```

#### 4 僵尸进程的回收
父进程通过调用pcntl_wait/pcntl_waitpid进行子进程回收

##### 4.1 通过while循环不断进行回收
通过while循环不断调用pcntl_wait()或者pcntl_waitpid()来不断地回收，这并不优雅，更重要的是使用while并不是真正的[异步]总是会有时间间隙在其中。

##### 4.2 通过信号进行回收
原理是：子进程退出时候，会给父进程发送SIGCHLD信号，然后在该信号处理函数中调用pcntl_wait()或者pcntl_waitpid()进行回收。

示例：
```
<?php
// fork
$i_pid = pcntl_fork();
// 下面是子进程逻辑.
if ( 0 == $i_pid ) { 
  sleep( 3 );
  exit();
}

// 下面是父进程逻辑..
pcntl_async_signals( true );
// 给进程安装信号...
pcntl_signal( SIGCHLD, function( $i_signo ) use( $i_pid ) { 
  switch ( $i_signo ) { 
    case SIGCHLD:
      echo "收到SIGCHLD信号，有子进程退出".PHP_EOL;
      pcntl_waitpid( $i_pid, $i_status, WNOHANG );
      print_r( $i_status );  
      break;
  }
} );
// 父进程while保持不退出.
while( true ) { 
  sleep( 1 );
}
```

参考资料
- ①、[老李在搞Workerman的日子里（五）](https://cloud.tencent.com/developer/article/1552426)
- ②、[关于执行两次pcntl_signal_dispatch的问题，烦请解惑](https://wenda.workerman.net/question/5721)
- ③、[pcntl_sigwaitinfo](https://www.php.net/manual/en/function.pcntl-sigwaitinfo.php#119613)