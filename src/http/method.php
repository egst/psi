<?php declare(strict_types = 1);

namespace Psi\Http;

enum Method: string {

    case get     = 'GET';
    case head    = 'HEAD';
    case post    = 'POST';
    case put     = 'PUT';
    case delete  = 'DELETE';
    case connect = 'CONNECT';
    case options = 'OPTIONS';
    case trace   = 'TRACE';
    case path    = 'PATH';

}
