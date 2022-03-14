<?php

while ($num = trim(fgets(STDIN))) {
    $elements = [];
    for ($i = 0; $i < $num; $i++) {
        $elements[] =  trim(fgets(STDIN));
    }

    $uniqElements = array_unique($elements);
    sort($uniqElements);
    foreach($uniqElements as $value) {
        echo $value . "\n";
    }
}