<?php

namespace Tivins\Core\Http;

/**
 * @deprecated
 * @see \Tivins\Core\Net\Http\Method
 */
enum Method
{
    case GET;
    case POST;
    case DELETE;
    case PUT;
    case CONNECT;
    case OPTIONS;
    case TRACE;
    case PATCH;
}