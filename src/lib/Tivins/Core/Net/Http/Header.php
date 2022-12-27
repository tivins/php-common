<?php

namespace Tivins\Core\Net\Http;

/**
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers
 */
enum Header: string
{
    case Accept = 'Accept';
    case AcceptLanguage = 'Accept-Language';
    case ETag = 'ETag';
    case AcceptEncoding = 'Accept-Encoding';
    case Location = 'Location';
    case Cookie = 'Cookie';
    case Authorization = 'Authorization';
    case ContentType = 'Content-Type';
    case ContentDisposition = 'Content-Disposition';
    case ContentLocation = 'Content-Location';
    case UserAgent = 'User-Agent';
    case Referer = 'Referer';
    case Connection = 'Connection';
    case UpgradeInsecureRequests = 'Upgrade-Insecure-Requests';
    case IfModifiedSince = 'If-Modified-Since';
    case IfNoneMatch = 'If-None-Match';
    case CacheControl = 'Cache-Control';
}