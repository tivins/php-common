<?php
use Tivins\Core\System\Terminal;
require 'vendor/autoload.php';
Terminal::sleep(2);
Terminal::sleep(2, "Remains: %.2f seconds.");
echo "Done.", PHP_EOL;
