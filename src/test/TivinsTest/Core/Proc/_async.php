<?php

use Tivins\Core\Proc\Command;
use Tivins\Core\Proc\ProcBackground;

require 'vendor/autoload.php';
$commands = [
    new Command(
        'git','clone', '-q',
        'e2:/srv/git/carnet',
        '/tmp/clone_'.time()
    ),
    new Command(__dir__ . '/_working.php'),
    new Command('composer','u','--no-interaction'),
];
foreach ($commands as $i => $command) {

    $proc = new ProcBackground(
        "Building container " . ($i + 1),
        "Container " . ($i + 1) . " built",
        "Failed to build container " . ($i + 1),
    );

    $proc->setShowStderr(true);
    $proc = $proc->run($command, 100000);
}
echo "Done.\n";
exit;