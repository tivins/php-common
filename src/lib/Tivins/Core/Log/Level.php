<?php

namespace Tivins\Core\Log;

enum Level: int
{
    case NONE      = 0;
    case EMERGENCY = 1;
    case ALERT     = 2;
    case CRITICAL  = 3;
    case ERROR     = 4;
    case WARNING   = 5;
    case NOTICE    = 6;
    case INFO      = 7;
    case DEBUG     = 8;
}