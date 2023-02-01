<?php

namespace Tivins\Dev\JS;

class Env
{
    private static Window $window;
    public static function init(): Window
    {
        return (self::$window = new Window());
    }
    public static function window(): Window {
        return self::$window;
    }

}