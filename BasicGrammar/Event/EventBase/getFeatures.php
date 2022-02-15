<?php
// Avoiding "select" method
$cfg = new EventConfig();
if ($cfg->avoidMethod("select")) {
    echo "`select' method avoided\n";
}

$base = new EventBase($cfg);

echo "Features:\n";
$features = $base->getFeatures();
var_dump($features);
($features & EventConfig::FEATURE_ET) and print("ET - edge-triggered IO\n");
($features & EventConfig::FEATURE_O1) and print("O1 - O(1) operation for adding/deletting events\n");
($features & EventConfig::FEATURE_FDS) and print("FDS - arbitrary file descriptor types, and not just sockets\n");
?>