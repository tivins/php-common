<?php

use Tivins\Core\Proc\Command;
use Tivins\Core\Proc\Process;
use Tivins\Core\Proc\ProcInfo;
use Tivins\Core\Proc\ProcBackground;

require 'vendor/autoload.php';
/*
echo "ABC";
sleep(1);
echo "\r\033[K\n";
die;
*/
/*
var_dump(\Tivins\Core\System\Terminal::getWidth());
echo "AAAA\n";
echo "BBBB\n";
echo "\033[F\033[F12\n\n";
exit;
*/
for ($i = 0; $i < 3; $i++) {

    $proc = new ProcBackground(
        "Building container " . ($i + 1),
        "Container " . ($i + 1) . " built",
    );
    // $proc = new Process();
    $cmdWork = new Command(
        'git','clone', '-q',
        'e2:/srv/git/carnet',
        '/tmp/clone_'.$i.'_'.time()
    );
    //$cmdWork = new Command(__dir__ . '/_working.php');
    //$cmdWork = new Command('composer','u');

    $proc  = $proc->run($cmdWork, 10000);
    var_dump($proc->ended-$proc->started);
    break;
}
echo "Done.\n";
exit;