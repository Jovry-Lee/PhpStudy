<?php
function show_Spanish($n, $m)
{
    return "The number {$n} is called {$m} in Spanish";
}

function map_Spanish($n, $m)
{
    return [$n => $m];
}

$a = [1, 2, 3, 4, 5];
$b = [1=>'uno', 3 => 'dos', 5 => 'cuatro'];

$c = array_map('show_Spanish', $a, $b);
print_r($c);

// $d = array_map('map_Spanish', $a , $b);
// print_r($d);
?>