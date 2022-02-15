<?php
$host = "127.0.0.1";
$port = 9999;
$content = "12345678123456781234567812345678";
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$conn = socket_connect($socket, $host, $port);
// payload = header + body
// "a"表示以Null填充字符串空白，"*"表示重复参数到末尾。
$body = pack("a*", $content);
$body_len = strlen($body);
echo $body . ' | ' . $body_len . PHP_EOL;
// pack()的N参数就表示按照网络字节序打包
$header = pack("N", $body_len);
$payload = $header . $body;
$send_ret = socket_write($socket, $payload, strlen($payload));
var_dump($send_ret);