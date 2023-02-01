<?php
use Tivins\Dev\JS\Env;

$window = Env::init();

//------------------------------

class Test
{
    public static string $test = 'hello';
    private static int $testPrivate = 42;
    public static function log(string $label, array $info): void
    {
        Env::window()->console->log($label, $info);
    }
}

Test::$test = 'world';
Test::log("yo", [1, 2, 3]);
$window->body->innerText = 'test';