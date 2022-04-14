<?php declare(strict_types = 1);

namespace Psi\Http;

use \Psi\Exception;
use \Psi\Http;

class Request {

    public function __construct (
        public Http\Scheme $scheme  = Http\Scheme::http,
        public Http\Method $method  = Http\Method::get,
        public string      $host    = 'localhost',
        public int         $port    = 80,
        public string      $path    = '',
        public array       $query   = [],
        public array       $body    = [],
        public array       $headers = [],
    ) {
        $this->path = trim($path, '/');
    }

    /** @throws Exception\InternalError */
    public static function get (): self {
        return new self(
            scheme:  Http::getScheme(),
            method:  Http::getMethod(),
            host:    Http::getHost(),
            port:    Http::getPort(),
            path:    Http::getPath(),
            query:   Http::getQuery(),
            body:    Http::getBody(),
            headers: Http::getHeaders(),
        );
    }

}
