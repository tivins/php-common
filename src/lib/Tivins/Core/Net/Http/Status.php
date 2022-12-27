<?php

namespace Tivins\Core\Net\Http;

enum Status: int
{
    case Continue = 100;

    // 200 - Successful responses
    case OK = 200;
    case Created = 201;
    case Accepted = 202;
    case NonAuthoritativeInformation = 203;
    case NoContent = 204;
    case ResetContent = 205;
    case PartialContent = 206;

    // 300 - Redirection messages
    case MultipleChoices = 300;
    case MovedPermanently = 301;
    case Found = 302;
    case SeeOther = 303;
    case NotModified = 304;
    case UseProxy = 305;
    case TemporaryRedirect = 307;

    // 400 - Client error responses
    case BadRequest = 400;
    case Unauthorized = 401;
    case PaymentRequired = 402;
    case Forbidden = 403;
    case NotFound = 404;
    case MethodNotAllowed = 405;
    case NotAcceptable = 406;
    case Conflict = 409;
    case Gone = 410;
    case LengthRequired = 411;
    case PreconditionFailed = 412;
    case RequestEntityTooLarge = 413;
    case RequestURITooLong = 414;
    case UnsupportedMediaType = 415;
    case RequestedRangeNotSatisfiable = 416;
    case ExpectationFailed = 417;

    // 500 - Server error responses
    case InternalServerError = 500;
    case NotImplemented = 501;
    case ServiceUnavailable = 503;

    public function isError(): bool
    {
        return $this->value / 100 >= 4;
    }
}
