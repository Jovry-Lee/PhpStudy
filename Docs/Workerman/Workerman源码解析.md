#### 1 基础
##### 1.1 Workerman的进程模型
Workerman的进程模型，这是一种单Master多Worker的进程模型，其中：
- Master进程fork出固定数量的Worker进程，并在服务运行后负责监控Worker进程的状态，比如Worker挂了后再重新拉起一个，又或者收到信号后reload全部Worker进程；
- Worker进程的职责就相对进程，每个Worker进程持有一个event-loop，负责监听各种网络事件；

##### 1.2 知识点
- 使用`0号信号`可以进行进程探活；
- SIGKILL信号不能忽略，也不能被捕获，是必须100%要响应的；
- Worker实例不是指worker进程；
- fork的子进程中通过srand及mt_srand函数可以将进程种子打乱。（计算机的随机数并不是真正的随机，而是一种伪随机数，子进程继承父进程的随机种子，因此需要打乱一下）;
- Workerman如何保证进程不退出：
    - Master进程：通过while(1);
    - Worker进程：通过event-loop

#### 2 守护进程化

源码如下：
```
protected static function daemonize() {
        // 如果配置中daemonize为false 或者 操作系统不是Linux
        // 那么直接返回
        if (!static::$daemonize || static::$_OS !== \OS_TYPE_LINUX) {
            return;
        }
        // 设置umask掩码，umask与chmod权限息息相关
        \umask(0);
        // fork进程，主进程退出执行，子进程继续执行，注意这里的子进程不是指Worker进程，而是Master进程。
        $pid = \pcntl_fork();
        if (-1 === $pid) {
            throw new Exception('Fork fail');
        } elseif ($pid > 0) {
            exit(0);
        }
        // 子进程（Master进程）使用posix_setsid()创建新会话和进程组
        // 这一句话便足以让当前进程脱离控制终端！
        if (-1 === \posix_setsid()) {
            throw new Exception("Setsid fail");
        }
        // 下面这句英文注释是 亮哥 写的，大概意思之前也说过，就是避免SVR4某些情况下情况下进程会再次获得控制终端
        // Fork again avoid SVR4 system regain the control of terminal.
        $pid = \pcntl_fork();
        // 主进程再次终止运行，最终的子进程会成为Master进程变成
        // daemon程序运行在后台，然后继续fork出Worker进程
        if (-1 === $pid) {
            throw new Exception("Fork fail");
        } elseif (0 !== $pid) {
            exit(0);
        }
    }
```

#### 3 信号安装

源码如下：
```
protected static function installSignal() {
        // 如果不是Linux不处理。
        if (static::$_OS !== \OS_TYPE_LINUX) {
            return;
        }
        // signalHandler便是具体的信号处理函数
        $signalHandler = '\Workerman\Worker::signalHandler';
        // stop
        // 捕获SIGINT信号，实现stop命令
        \pcntl_signal(\SIGINT, $signalHandler, false);
        // graceful stop
        // 捕获SIGTERM信号，实现柔性停止
        \pcntl_signal(\SIGTERM, $signalHandler, false);
        // reload
        // 捕获SIGUSR1，实现reload
        \pcntl_signal(\SIGUSR1, $signalHandler, false);
        // graceful reload
        // 捕获SIGQUIT信号，实现柔性加载
        \pcntl_signal(\SIGQUIT, $signalHandler, false);
        // status
        // 捕获SIGUSR2信号，实现status
        \pcntl_signal(\SIGUSR2, $signalHandler, false);
        // connection status
        // SIGIO信号
        \pcntl_signal(\SIGIO, $signalHandler, false);
        // ignore
        // 捕获SIGPIPE信号，忽略掉所有管道事件
        \pcntl_signal(\SIGPIPE, \SIG_IGN, false);
    }

    // 然后我们需要再继续关注下signalHandler函数，这个函数里是对各个信号响应的具体业务方法
    public static function signalHandler($signal) {
        switch ($signal) {
            // Stop. 实际上case SIGINT和case SIGTERM的逻辑非常简单，唯一区别就是\$_gracefulStop参数
            // static::stopAll()这个函数实现了Workerman停止的逻辑
            case \SIGINT:
                static::$_gracefulStop = false;
                static::stopAll();
                break;
            // Graceful stop.
            case \SIGTERM:
                static::$_gracefulStop = true;
                static::stopAll();
                break;
            // Reload. 这个函数实现了对所有Worker进程reload热加载，而在reload函数之前，首先需要使用getAllWorkerPids()，获取到所有待reload的Worker进程的pid们
            case \SIGQUIT:
            case \SIGUSR1:
                if($signal === \SIGQUIT){
                    static::$_gracefulStop = true;
                }else{
                    static::$_gracefulStop = false;
                }
                static::$_pidsToRestart = static::getAllWorkerPids();
                static::reload();
                break;
            // Show status.
            case \SIGUSR2:
                static::writeStatisticsToStatusFile();
                break;
            // Show connection status.
            case \SIGIO:
                static::writeConnectionsStatisticsToStatusFile();
                break;
        }
    }
```

#### 4 stopAll()函数
针对Master进程和Worker进程的处理，信号处理函数是分开的。

源码如下：
```
public static function stopAll() {
        // 首先将\$_status成员属性设置为shutdown状态，估计是这个成员属性在其他地方必须要被用到
        static::$_status = static::STATUS_SHUTDOWN;

        // Master进程
        if (static::$_masterPid === \posix_getpid()) {
            // 使用log函数在log文件里打一行日志...
            static::log("Workerman[" . \basename(static::$_startFile) . "] stopping ...");
            // 获取到当前Master进程的所有Worker进程的pid们
            $worker_pid_array = static::getAllWorkerPids();
            // Send stop signal to all child processes.
            if (static::$_gracefulStop) {
                $sig = \SIGTERM;
            } else {
                $sig = \SIGINT;
            }
            // 循环遍历Worker进程的pid数组，然后使用posix_kill向每一个子进程发送SIGTERM信号或者SIGINT信号
            foreach ($worker_pid_array as $worker_pid) {
                \posix_kill($worker_pid, $sig);
                // 定时向子进程发送SIGKILL信号（该信号不能忽略，也不能被捕获，是必须100%要响应的），保证子进程被干掉。
                if(!static::$_gracefulStop){
                    Timer::add(static::KILL_WORKER_TIMER_TIME, '\posix_kill', array($worker_pid, \SIGKILL), false);
                }
            }
            // 1秒钟后检测进程是否还活着，如果确定都挂了，则将进程id从保存的数组中unset掉。
            Timer::add(1, "\\Workerman\\Worker::checkIfChildRunning");
            
            // 删除掉所有statistics文件，workerman会在后台运行期间将一些数据记录到statistics文件中去，当我们需要在服务运行期间查看一些数据的时候，就是从这个文件获取到的。
            if (\is_file(static::$_statisticsFile)) {
                @\unlink(static::$_statisticsFile);
            }
        } // 子进程。
        else {
            // Execute exit.
            // static::$_workers保存的是Worker实例，并不是Worker进程。
            foreach (static::$_workers as $worker) {
                // 由于一个Worker实例可能会有多个子进程，每个子进程受到stop后都会停止当前Worker实例，因此，如果一个子进程已经停止了，其余子进程则不需要再次停止当前Worker实例。
                // 此处的stop，仅仅是为了触发onWorkerStop回调，并不是exit进程。
                if(!$worker->stopping){
                    // 所以我们关注重点需要集中到stop()函数方法上去了
                    $worker->stop();
                    $worker->stopping = true;
                }
            }
            // 每个Worker进程都会持有一个event-loop，要关闭worker进程，就需要把关于网络链接相关的东西给关掉。
            if (!static::$_gracefulStop || ConnectionInterface::$statistics['connection_count'] <= 0) {
                static::$_workers = array();
                if (static::$globalEvent) {
                    static::$globalEvent->destroy();
                }
                // 到这里，才算是真正地退出子进程们...
                exit(0);
            }
        }
    }

    // 通过给进程发送0号信号判断进程是否存在。
    public static function checkIfChildRunning() {
        foreach (static::$_pidMap as $worker_id => $worker_pid_array) {
            foreach ($worker_pid_array as $pid => $worker_pid) {
                // 使用0号信号进行进程探活
                if (!\posix_kill($pid, 0)) {
                    unset(static::$_pidMap[$worker_id][$pid]);
                }
            }
        }
    }
    
    // 关闭Worker实例
    public function stop() {
        // 触发onWorkerStop事件。
        if ($this->onWorkerStop) {
            try {
                // 使用call_user_func实现PHP中的on回调
                \call_user_func($this->onWorkerStop, $this);
            } catch (\Exception $e) {
                static::log($e);
                exit(250);
            } catch (\Error $e) {
                static::log($e);
                exit(250);
            }
        }

        // Remove listener for server socket.
        $this->unlisten();
        // Close all connections for the worker.
        if (!static::$_gracefulStop) {
            foreach ($this->connections as $connection) {
                $connection->close();
            }
        }
        // Clear callback.
        $this->onMessage = $this->onClose = $this->onError = $this->onBufferDrain = $this->onBufferFull = null;
    }
```

##### 4.1 平滑停止和非平滑停止的区别体现在哪儿？
在Workerman里，使用SIGINT实现粗暴stop，使用SIGTERM实现优雅stop。SIGINT是键盘上的Ctrl+C
组合键产生的。这两个信号的默认动作都是直接终止程序，即不额外安装信号处理捕捉这两个信号，一旦捕捉了两个信号，实际上他们都会让你程序中代码运行完毕，然后再去关闭程序。

示例：
```
<?php
echo posix_getpid().PHP_EOL;
pcntl_async_signals( true );
// 给进程安装信号...
pcntl_signal( SIGTERM, function() {
  for( $i = 1; $i <= 10; $i++ ){
    echo $i.PHP_EOL;
    sleep( 1 );
  }
  exit; 
} );
pcntl_signal( SIGINT, function() {
   for( $i = 1; $i <= 10; $i++ ){
    echo $i.PHP_EOL;
    sleep( 1 );
  }
  exit; 
} );
// while保持进程不要退出..
while ( true ) { 
  sleep( 1 );
}
```
运行结果：
```
# Ctrl+C
seven@SevendeMacBook-Pro PhpNetworkProgram % php GracefulStop.php 
4512
^C1
2
3
4
5
6
7
8
9
10

# 另起一个终端，发送kill -15 4530
seven@SevendeMacBook-Pro PhpNetworkProgram % php GracefulStop.php
4530
1
2
3
4
5
6
7
8
9
10
```

#### 5 forkWorkers()函数
源码：
```
protected static function forkWorkers() {
        // 根据操作系统的不同，走不同的fork方法
        if (static::$_OS === \OS_TYPE_LINUX) {
            static::forkWorkersForLinux();
        } else {
            static::forkWorkersForWindows();
        }
}

protected static function forkWorkersForLinux() {
        // 遍历Worker实例进行处理，注意，这里不是Worker进程。
        foreach (static::$_workers as $worker) {
            // worker实例有一个属性叫做name，即Worker的名称，默认为none
            if (static::$_status === static::STATUS_STARTING) {                
                if (empty($worker->name)) {
                    $worker->name = $worker->getSocketName();
                }
                $worker_name_length = \strlen($worker->name);
                if (static::$_maxWorkerNameLength < $worker_name_length) {
                    static::$_maxWorkerNameLength = $worker_name_length;
                }
            }

            // 根据Worker对象的count属性，fork出固定数量的worker进程。一个Worker实例，拥有以Master进程，Master进程需要fork出count数量的Worker子进程。
            while (\count(static::$_pidMap[$worker->workerId]) < $worker->count) {
                static::forkOneWorkerForLinux($worker);
            }
        }
}

protected static function forkOneWorkerForLinux(self $worker) {
        // 获取可用的Worker进程的id，id是表示的是：一个Worker实例中每一个子worker进程的id，比如有fork了4个子worker进程，那么这四个子worker进程的$id就是0 1 2 3。
        $id = static::getId($worker->workerId, 0);
        if ($id === false) {
            return;
        }
        
        $pid = \pcntl_fork();
        if ($pid > 0) { // 父进程。
            // 父进程主要就是保存 $_pidMap 和 $_idMap 两个数组即可
            static::$_pidMap[$worker->workerId][$pid] = $pid;
            static::$_idMap[$worker->workerId][$id]   = $pid;
        } elseif (0 === $pid) { // 子进程。
            // 打乱随机种子，保证获取的伪随机数的代码会得到不同的伪随机数。
            \srand();
            \mt_srand();
            // 如果是端口复用，就直接listen。
            if ($worker->reusePort) {
                $worker->listen();
            }
            // resetStd()函数里，对各种输入、输出，该关闭的关闭，该重定向的重定向.
            if (static::$_status === static::STATUS_STARTING) {
                static::resetStd();
            }
            static::$_pidMap  = array();
            
            // 移除其他worker的监听。干啥用？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？
            foreach(static::$_workers as $key => $one_worker) {
                if ($one_worker->workerId !== $worker->workerId) {
                    $one_worker->unlisten();
                    unset(static::$_workers[$key]);
                }
            }

            // 清除掉所有原来的定时器.
            Timer::delAll();
            // 给进程起一个名字，方便在ps -ef的时候辨别
            static::setProcessTitle('WorkerMan: worker process  ' . $worker->name . ' ' . $worker->getSocketName());
            $worker->setUserAndGroup();
            $worker->id = $id;
            // run()函数中，就是 event-loop 了！
            $worker->run();
            $err = new Exception('event-loop exited');
            static::log($err);
            exit(250);
        } else {
            throw new Exception("forkOneWorker fail");
        }
}
```

#### 6 MoniterWorkers()函数
源码：
```
protected static function monitorWorkers() {
        if (static::$_OS === \OS_TYPE_LINUX) {
            static::monitorWorkersForLinux();
        } else {
            static::monitorWorkersForWindows();
        }
}

protected static function monitorWorkersForLinux() {
        // 首先将整个worker实例的状态设置为running.
        static::$_status = static::STATUS_RUNNING;
        
        // Master进程就靠while保证不退出并持续对Worker进程进行监控
        while (1) {
            // 分发信号，使信号监听函数生效。
            \pcntl_signal_dispatch();
            // 挂起当前进程的执行，直到子进程退出，或直到发送信号
            $status = 0;
            // 使用wait来回收子进程，避免僵尸进程
            $pid    = \pcntl_wait($status, \WUNTRACED);

            // 再次调用pcntl_signal_dispatch分发信号，防止信号延迟/丢失。
            \pcntl_signal_dispatch();
            
            // 子进程退出。
            if ($pid > 0) {
                // 一个worker子进程已经退出了，所以这里要把之前的$_pidMap等数组中信息中与刚死掉的那个worker子进程相关的所有信息全部清理掉
                foreach (static::$_pidMap as $worker_id => $worker_pid_array) {
                    if (isset($worker_pid_array[$pid])) {
                        $worker = static::$_workers[$worker_id];
                        // Exit status.
                        if ($status !== 0) {
                            static::log("worker[" . $worker->name . ":$pid] exit with status $status");
                        }

                        // For Statistics.
                        if (!isset(static::$_globalStatistics['worker_exit_info'][$worker_id][$status])) {
                            static::$_globalStatistics['worker_exit_info'][$worker_id][$status] = 0;
                        }
                        ++static::$_globalStatistics['worker_exit_info'][$worker_id][$status];

                        // Clear process data.
                        unset(static::$_pidMap[$worker_id][$pid]);
                        // Mark id is available.
                        $id                              = static::getId($worker_id, $pid);
                        static::$_idMap[$worker_id][$id] = 0;

                        break;
                    }
                }
                // Is still running state then fork a new worker process.
                // worker进程退出可能并不因为是停止服务，还有一种情况就是reload，又或者worker子进程响应过xxx次请求后自动销毁并拉起一个新的（不知道wm有没有这功能），这些情况下除了要回收垃圾信息外，到最后还要再重新拉起一个新的worker子进程来补充进来。
                if (static::$_status !== static::STATUS_SHUTDOWN) {
                    // 通过while循环+判断count属性来实现的
                    static::forkWorkers();
                    // If reloading continue
                    // 这里有个reload，但是reload如果要运行，有一个条件那就是当前退出的worker子进程必须要在 $_pidsToRestart数组中
                    if (isset(static::$_pidsToRestart[$pid])) {
                        unset(static::$_pidsToRestart[$pid]);
                        
                        // 每当一个子进程退出后，并且该子进程在$_pidsToRestart数组里，那么Master进程就要执行一下reload。
                        static::reload();
                    }
                }
            }

            // 如果是真要退出服务了。。。清理所有垃圾数据，然后再退出
            if (static::$_status === static::STATUS_SHUTDOWN && !static::getAllWorkerPids()) {
                static::exitAndClearAll();
            }
        }
}
```

#### 7 reload()函数

源码：
```
protected static function reload() {
        // For master process.
        if (static::$_masterPid === \posix_getpid()) {
            // 一、设置当前状态为reloading
            // 二、通过call_user_func触发onWorkerReload回调函数
            if (static::$_status !== static::STATUS_RELOADING && static::$_status !== static::STATUS_SHUTDOWN) {
                static::log("Workerman[" . \basename(static::$_startFile) . "] reloading");
                static::$_status = static::STATUS_RELOADING;
                // Try to emit onMasterReload callback.
                if (static::$onMasterReload) {
                    try {
                        // 这个技巧值得注意！
                        \call_user_func(static::$onMasterReload);
                    } catch (\Exception $e) {
                        static::log($e);
                        exit(250);
                    } catch (\Error $e) {
                        static::log($e);
                        exit(250);
                    }
                    static::initId();
                }
            }
            
            if (static::$_gracefulStop) {
                $sig = \SIGQUIT;
            } else {
                $sig = \SIGUSR1;
            }
            // Send reload signal to all child processes.
            $reloadable_pid_array = array();
            // $_pidMap中保存了当前所有Worker实例的所有worker子进程pid
            foreach (static::$_pidMap as $worker_id => $worker_pid_array) {
                // 当前worker实例
                $worker = static::$_workers[$worker_id];
                // reloadable这个属性用于设置当前Worker实例是否可以reload，即收到reload信号后是否退出重启。不设置默认为true，收到reload信号后自动重启进程
                if ($worker->reloadable) {
                    foreach ($worker_pid_array as $pid) {
                        $reloadable_pid_array[$pid] = $pid;
                    }
                } else {
                    foreach ($worker_pid_array as $pid) {
                        // Send reload signal to a worker process which reloadable is false.
                        \posix_kill($pid, $sig);
                    }
                }
            }

            // 这个地方非常非常有意思，因为这里这个数组将会成为结束执行的条件
            static::$_pidsToRestart = \array_intersect(static::$_pidsToRestart, $reloadable_pid_array);

            // Reload complete.
            // 若$_pidsToRestart已经是空了，reload方法就彻底结束执行了
            if (empty(static::$_pidsToRestart)) {
                if (static::$_status !== static::STATUS_SHUTDOWN) {
                    static::$_status = static::STATUS_RUNNING;
                }
                return;
            }
            // Continue reload.
            $one_worker_pid = \current(static::$_pidsToRestart);
            // Send reload signal to a worker process.
            // 向子进程pid发送sigquit/sigusr1信号，当捕捉到sigquit/sigusr1后，信号处理器里就会再次执行static::reload()！即static::reload()中Master代码段+signal-handler共同组成了一个循环逻辑！而结束这个循环的条件就是static::$_pidsToRestart数组w为空！
            \posix_kill($one_worker_pid, $sig);
            // If the process does not exit after static::KILL_WORKER_TIMER_TIME seconds try to kill it.
            // 保证子进程退出。
            if(!static::$_gracefulStop){
                Timer::add(static::KILL_WORKER_TIMER_TIME, '\posix_kill', array($one_worker_pid, \SIGKILL), false);
            }
        } // For child processes.
        else {
            \reset(static::$_workers);
            $worker = \current(static::$_workers);
            // Try to emit onWorkerReload callback.
            if ($worker->onWorkerReload) {
                try {
                    \call_user_func($worker->onWorkerReload, $worker);
                } catch (\Exception $e) {
                    static::log($e);
                    exit(250);
                } catch (\Error $e) {
                    static::log($e);
                    exit(250);
                }
            }
            // 子进程执行realod()的时候，如果reloadable会true，就会exit自己，exit自己会让Master进程收到sigchld信号，master进程中monitorWorkers()方法的pcntl_wait
会开始执行，然后Master进程知道有子进程退出后，会做下善后工作，然后Master进程就会再fork个新的子进程出来顶替原来exit掉的子进程！
            if ($worker->reloadable) {
                static::stopAll();
            }
       }
}
```
