<?php
pcntl_signal(SIGALRM, function () {
    pcntl_alarm(5);
    echo 'Received an alarm signal !' . PHP_EOL;
}, false);

pcntl_alarm(5);

while (true) {
    pcntl_signal_dispatch();
    sleep(1);
}