<?php

namespace Tivins\Core\Http;

enum Method
{
    case NONE;
    case GET;
    case POST;
    case DELETE;
    case PUT;
}