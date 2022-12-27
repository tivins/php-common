<?php

namespace Tivins\Core\Net\Http;

/**
 * HTTP defines a set of request methods to indicate the
 * desired action to be performed for a given resource.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7231#section-4
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
 */
enum Method: string
{
    case GET = 'GET';
    case POST = 'POST';
    case DELETE = 'DELETE';
    case PUT = 'PUT';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
    case PATCH = 'PATCH';
}