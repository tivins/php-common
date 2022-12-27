<?php

namespace Tivins\Core;

enum ColorFormat: string
{
    case HEX = '%02x%02x%02x';
    case TTY = '%d;%d;%d';
    case RGB = 'rgb(%d,%d,%d)';
}
