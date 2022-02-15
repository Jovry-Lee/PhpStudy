<?php


$string = pack('L4', 1134242, 22423421, 142423423, 14234234);
var_dump(unpack('Ll1/Ll2/Ll3/Ll4', $string)); //可以指定key，用/分割