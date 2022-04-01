<?php

namespace Tivins\Core\Http;

enum ContentType: string
{
    case ALL = '*';
    case HTML = 'text/html';
    case TEXT = 'text/plain';
    case JS = 'text/javascript';
    case CSS = 'text/css';
    case JSON = 'application/json';
    case JPEG = 'image/jpeg';
    case PNG = 'image/png';
    case GIF = 'image/gif';
    case MP3 = 'audio/mpeg';
    case MP4 = 'video/mp4';
}