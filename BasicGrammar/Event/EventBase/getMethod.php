<?php
$cfg = new EventConfig();
if ($cfg->avoidMethod("select")) {
    echo "`select' method avoided\n";
}

// Create event_base associated with the config
$base = new EventBase($cfg);
echo "Event method used: ", $base->getMethod(), PHP_EOL;

?>