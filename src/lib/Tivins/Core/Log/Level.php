<?php

namespace Tivins\Core\Log;

enum Level: int
{
    case NONE = 0;
    case DANGER = 1;
    case WARNING = 2;
    case SUCCESS = 3;
    case INFO = 4;
    case DEBUG = 5;
}