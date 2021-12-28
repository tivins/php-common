<?php

namespace Tivins\Core\Http;

/**
 * HTTP defines a set of request methods to indicate the desired action to be performed for a given resource.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7231#section-4
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
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