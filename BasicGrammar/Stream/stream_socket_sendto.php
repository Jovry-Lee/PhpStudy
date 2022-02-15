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