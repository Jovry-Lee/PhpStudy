### 1. 简介
### 2. PCNTL方法详解
#### 2.1 pcntl_signal_dispatch
##### 2.1.1 功能
调用每个等待信号通过pcntl_signal() 安装的处理器。
##### 2.1.2 说明
```
pcntl_signal_dispatch ( void ) : bool
```
返回值:
成功时返回 TRUE， 或者在失败时返回 FALSE。

##### 2.1.3 示例
```
pcntl_signal_dispatch.php
<?php
echo "安装信号处理器...\n";
pcntl_signal(SIGHUP,  function($signo) {
    echo "信号处理器被调用\n";
});

echo "为自己生成SIGHUP信号...\n";
posix_kill(posix_getpid(), SIGHUP);

echo "分发...\n";
pcntl_signal_dispatch();

echo "完成\n";

?>
```
测试:
```
cdyf@jumei:~/tutorial/PHP_Basic/Pcntl$ php pcntl_signal_dispatch.php
安装信号处理器...
为自己生成SIGHUP信号...
分发...
信号处理器被调用
完成
```
#### 2.2 pcntl_signal
##### 2.2.1 功能
安装一个信号处理器
##### 2.2.2 说明
```
pcntl_signal ( int $signo , callback $handler [, bool $restart_syscalls = true ] ) : bool
```
入参:
- $signo
信号编号。
- $handler
信号处理器可以是用户创建的函数或方法的名字，也可以是系统常量 SIG_IGN（译注：忽略信号处理程序）或SIG_DFL（默认信号处理程序）.
- $restart_syscalls
指定当信号到达时系统调用重启是否可用。（译注：经查资料，此参数意为系统调用被信号打断时，系统调用是否从开始处重新开始，但根据http://bugs.php.net/bug.php?id=52121，此参数存在bug无效。）

返回值:
成功时返回 TRUE， 或者在失败时返回 FALSE。

##### 2.2.3 注意
- a. 其实官方的pcntl_signal性能极差，主要是PHP的函数无法直接注册到操作系统信号设置中，所以pcntl信号需要依赖tick机制来完成(参考资料-c)。
- b. pcntl_signal的实现原理是，触发信号后先将信号加入一个队列中。然后在PHP的ticks回调函数中不断检查是否有信号，如果有信号就执行PHP中指定的回调函数，如果没有则跳出函数。ticks=1表示每执行1行PHP代码就回调此函数。实际上大部分时间都没有信号产生，但ticks的函数一直会执行。如果一个服务器程序1秒中接收1000次请求，平均每个请求要执行1000行PHP代码。那么PHP的pcntl_signal，就带来了额外的 1000 * 1000，也就是100万次空的函数调用。这样会浪费大量的CPU资源。
- c. 比较好的做法是去掉ticks，转而使用pcntl_signal_dispatch，在代码循环中自行处理信号。

##### 2.2.4 参考资料
- a. [pcntl-signal](http://php.net/manual/en/function.pcntl-signal.php)
- b. [PHP系统编程--03.PHP进程信号处理](https://www.cnblogs.com/linzhenjie/p/5485436.html)
- c. [PHP官方的pcntl_signal性能极差](http://rango.swoole.com/archives/364)

##### 2.2.5 示例
```
<?php
//使用ticks需要PHP 4.3.0以上版本
declare(ticks = 1);

//信号处理函数
function sig_handler($signo)
{

    switch ($signo) {
        case SIGTERM:
            // 处理SIGTERM信号
            echo "处理SIGTERM信息";
            exit;
            break;
        case SIGHUP:
            //处理SIGHUP信号
            echo "处理SIGHUP信号";
            break;
        case SIGUSR1:
            echo "Caught SIGUSR1...\n";
            break;
        default:
            // 处理所有其他信号
            echo "处理所有其他信号";
    }

}

echo "Installing signal handler...\n";

//安装信号处理器
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP,  "sig_handler");
pcntl_signal(SIGUSR1, "sig_handler");

// 或者在PHP 4.3.0以上版本可以使用对象方法
// pcntl_signal(SIGUSR1, array($obj, "do_something");

echo "Generating signal SIGTERM to self...\n";

//向当前进程发送SIGUSR1信号
posix_kill(posix_getpid(), SIGUSR1);

echo "Done\n"

?>
```
测试:
```
cdyf@jumei:~/tutorial/PHP_Basic/Pcntl$ php pcntl_signal.php
Installing signal handler...
Generating signal SIGTERM to self...
Caught SIGUSR1...
Done
```

#### 2.3 pcntl_alarm
##### 2.3.1 功能
为进程设置一个alarm闹钟信号.
创建一个计时器，在指定的秒数后向进程发送一个SIGALRM信号。每次对 pcntl_alarm()的调用都会取消之前设置的alarm信号。
##### 2.3.2 说明
```
pcntl_alarm ( int $seconds ) : int
```
入参:
- $seconds
等待的秒数。如果seconds设置为0,将不会创建alarm信号。

返回值:
返回上次alarm调度（离alarm信号发送）剩余的秒数，或者之前没有alarm调度（译注：或者之前调度已完成） 时返回0。

##### 2.3.3 注意
- a.每次对 pcntl_alarm()的调用都会取消之前设置的alarm信号
- b.如果seconds设置为0,将不会创建alarm信号

##### 2.3.4 示例
```
<?php
pcntl_signal(SIGALRM, function () {
    echo 'Received an alarm signal !' . PHP_EOL;
}, false);

pcntl_alarm(5);

while (true) {
    pcntl_signal_dispatch();
    sleep(1);
}
```
测试结果:
5秒过后终端输出
```
cdyf@jumei:~/tutorial/PHP_Basic/Pcntl$ php pcntl_alarm.php
Received an alarm signal !
```


































