<?php

use Tivins\Core\Proc\Command;
use Tivins\Core\Proc\Proc;
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

    $hooks = new  ProcBackground(
        "Building container " . ($i + 1),
        "Container " . ($i + 1) . " built",
    );
    $hooks->setCallbackFrequency(2000);
    $proc  = Proc::run(new Command(__dir__ . '/_working.php', rand(4,8)), $hooks);
}
echo "Done.\n";
exit;