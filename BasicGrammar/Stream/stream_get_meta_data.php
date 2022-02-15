<?php
// creating the socket...
$socket = stream_socket_server('tcp://127.0.0.1:8085', $errno, $errstr);
if (!$socket)
{
    echo "$errstr ($errno)<br />\n";
}
else
{
    // while there is connection, i'll receive it... if I didn't receive a message within $nbSecondsIdle seconds, the following function will stop.
    while ($conn = @stream_socket_accept($socket,5))
    {
        $message= fread($conn, 1024);
        var_dump(stream_get_meta_data($socket));
        echo 'I have received that : '.$message;
        fputs ($conn, "OK\n");
        fclose ($conn);
    }
    fclose($socket);
}