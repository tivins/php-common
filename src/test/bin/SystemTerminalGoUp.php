<?php

use Tivins\Core\Chrono;
use Tivins\Core\System\Terminal;

require 'vendor/autoload.php';

$array = [
    'part1' => ['action1', 'action2', 'action3'],
    'part2' => ['action4', 'action5'],
];

$chrono = (new Chrono())->start();
foreach ($array as $partName => $actions) {
    $actionChrono = (new Chrono())->start();
    echo " 🔧 $partName\n";
    foreach ($actions as $action) {
        echo " └─ processing $action ...\n";
        usleep(rand(400000, 600000)); // fake processing
        Terminal::goUpClean(1);
    }
    Terminal::goUpClean(1);
    echo " ✅ $partName (" . number_format($actionChrono->get(), 2) . " s.)\n";
}
echo "All tasks finished in " . number_format($chrono->get(), 2) . " seconds.\n";