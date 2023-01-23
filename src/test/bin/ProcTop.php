<?php

use Tivins\Core\Proc\Process;

require 'vendor/autoload.php';

class MyProcess extends Process
{
    public function onUpdate(array $status, array $received): void
    {
        $in = $received[Process::STDOUT];
        if (!$in) return;
        echo $in;
    }
}

$cmd = new \Tivins\Core\Proc\Command('top');

// Comment
echo 'ok';