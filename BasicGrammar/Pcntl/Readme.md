### 1. 简介
PHP的进程控制支持实现了Unix方式的进程创建, 程序执行, 信号处理以及进程的中断。 进程控制不能被应用在Web服务器环境，当其被用于Web服务环境时可能会带来意外的结果(编译时,只编译到CLI版本的PHP中，而不是Apache模块中)。
PCNTL现在使用了ticks作为信号处理的回调机制，ticks在速度上远远超过了之前的处理机制。 这个变化与“用户ticks”遵循了相同的语义。您可以使用declare() 语句在程序中指定允许发生回调的位置。这使得我们对异步事件处理的开销最小化。在编译PHP时 启用pcntl将始终承担这种开销，不论您的脚本中是否真正使用了pcntl。
### 2. 预定义常量
使用kill -l命令可查看当前系统支持所有信号以及编码
本机系统为Ubuntu16.04
```
 1) SIGHUP	 2) SIGINT	 3) SIGQUIT	 4) SIGILL	 5) SIGTRAP
 6) SIGABRT	 7) SIGBUS	 8) SIGFPE	 9) SIGKILL	10) SIGUSR1
11) SIGSEGV	12) SIGUSR2	13) SIGPIPE	14) SIGALRM	15) SIGTERM
16) SIGSTKFLT	17) SIGCHLD	18) SIGCONT	19) SIGSTOP	20) SIGTSTP
21) SIGTTIN	22) SIGTTOU	23) SIGURG	24) SIGXCPU	25) SIGXFSZ
26) SIGVTALRM	27) SIGPROF	28) SIGWINCH	29) SIGIO	30) SIGPWR
31) SIGSYS	34) SIGRTMIN	35) SIGRTMIN+1	36) SIGRTMIN+2	37) SIGRTMIN+3
38) SIGRTMIN+4	39) SIGRTMIN+5	40) SIGRTMIN+6	41) SIGRTMIN+7	42) SIGRTMIN+8
43) SIGRTMIN+9	44) SIGRTMIN+10	45) SIGRTMIN+11	46) SIGRTMIN+12	47) SIGRTMIN+13
48) SIGRTMIN+14	49) SIGRTMIN+15	50) SIGRTMAX-14	51) SIGRTMAX-13	52) SIGRTMAX-12
53) SIGRTMAX-11	54) SIGRTMAX-10	55) SIGRTMAX-9	56) SIGRTMAX-8	57) SIGRTMAX-7
58) SIGRTMAX-6	59) SIGRTMAX-5	60) SIGRTMAX-4	61) SIGRTMAX-3	62) SIGRTMAX-2
63) SIGRTMAX-1	64) SIGRTMAX
```
### 3. 示例

