<?php

use Tivins\Core\Proc\AsciiProcess;
use Tivins\Core\Proc\Command;
use Tivins\Core\System\File;

require 'vendor/autoload.php';

// Create a simple PHP file for example.
$tmpFile = tempnam('/tmp', 'test');
File::save($tmpFile, '<?' . 'php' . "\n" . 'echo "Hello"; sleep(1); echo "Word\n";');

// Build cast with command 'php /path/to/file.php'
$cast = AsciiProcess::buildCast(new Command('php', $tmpFile));
echo $cast . PHP_EOL;

// Remove temp file.
File::delete($tmpFile);

