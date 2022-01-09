<?php

namespace Tivins\Core\Proc;

use Tivins\Core\Proc\Command;
use Tivins\Core\Proc\ProcBackground;

require 'vendor/autoload.php';
$commands = [
    new Command(
        'git', 'clone', '--progress',
        'e2:/srv/git/carnet',
        '/tmp/clone_' . time()
    ),
    new Command(__dir__ . '/_working.php'),
    new Command('composer', 'u', '--no-interaction'),
];

foreach ($commands as $i => $command) {

    $proc = new ProcBackground(
        "Building container " . ($i + 1),
        "Container " . ($i + 1) . " built",
        "Failed to build container " . ($i + 1),
    );

    $proc->setShowStderr(true);
    $proc->setShowStdout(true);
    $proc = $proc->run($command, 100000);

    echo json_encode($proc->stderr) . "\n";
    $lines = preg_split('~[\r\n]~', $proc->stderr, 0, PREG_SPLIT_OFFSET_CAPTURE);
    $lines = array_map(fn($info) => new Line($info[0], substr($proc->stderr, $info[1] - 1, 1)), $lines);
    // var_dump($lines);
    // array_map(function(array $info) use($proc) {
    //     echo json_encode($info[0]) . "\n";
    //     echo 'Char= ' . json_encode(substr($proc->stderr, $info[1] - 1, 1)) . "\n\n";
    // }, $lines);
    die;
}
echo "Done.\n";
exit;