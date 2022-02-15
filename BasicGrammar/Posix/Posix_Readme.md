### 1. 简介
### 2. Posix方法详解
#### 2.1 posix_ttyname
##### 2.1.1 功能
返回文件描述符fd上打开的当前终端设备的绝对路径的字符串。
##### 2.1.2 说明
```
posix_ttyname ( mixed $fd ) : string
```
入参:
- $fd
文件描述符，它可以是文件资源，也可以是整数。整数将被假定为可以直接传递到底层系统调用的文件描述符。

返回值:
成功时，返回fd的绝对路径字符串。如果失败，返回FALSE

##### 2.1.3 示例
```
<?php
echo posix_ttyname(STDOUT);
```

测试结果:
```
cdyf@jumei:~/tutorial/PHP_Basic/Pcntl$ php pcntl_signal.php
/dev/pts/23
```