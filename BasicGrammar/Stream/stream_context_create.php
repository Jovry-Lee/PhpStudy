<?php
$opts = array(
    'http'=>array(
        'method'=>"GET",
        'header'=>"Accept-language: en\r\n" .
            "Cookie: foo=bar\r\n"
    )
);

$context = stream_context_create($opts);

/* Sends an http request to www.example.com
   with additional headers shown above */
$fp = fopen('http://www.example.com', 'r', false, $context);
fpassthru($fp);
fclose($fp);
?>