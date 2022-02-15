<?php

$string = pack('L4', 1134242, 22423421, 142423423, 14234234);
var_dump(unpack('L4', $string));
