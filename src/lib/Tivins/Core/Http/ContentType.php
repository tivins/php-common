<?php

namespace Tivins\Core\Http;

enum ContentType: string
{
    case HTML = 'text/html';
    case TEXT = 'text/plain';
    case JS = 'text/javascript';
    case CSS = 'text/css';
    case JSON = 'application/json';
}