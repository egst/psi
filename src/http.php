<?php declare(strict_types = 1);

namespace Psi;

use \Psi\Exception;
use \Psi\Http;
use \Psi\Std\Arr;
use \Psi\Std\Cast;
use \Psi\Std\Check;

/**
 *  These methods might or might not be available outside of a web server,
 *  but only the ones that can't deliver a sensible result value
 *  do throw in non-web-server environments.
 */
class Http {

    public static function getScheme (): Http\Scheme {
        $secure =
            ($_SERVER['HTTPS']                  ?? null) == 'on'    ||
            ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null) == 'https' ||
            ($_SERVER['HTTP_X_FORWARDED_SSH']   ?? null) == 'on';
        return $secure
            ? Http\Scheme::https
            : Http\Scheme::http;
    }

    public static function getMethod (): Http\Method {
        return Http\Method::tryFrom(Cast::string($_SERVER['REQUEST_METHOD'] ?? null)) ?? Http\Method::get;
    }

    public static function getHost (): string {
        return Cast::string($_SERVER['SERVER_NAME'] ?? null);
    }

    public static function getPort (): int {
        return Cast::int($_SERVER['SERVER_PORT'] ?? null);
    }

    /** Without the leading and trailing slashes. */
    public static function getPath (): string {
        return trim(explode('?', Cast::string($_SERVER['REQUEST_URI'] ?? null))[0], '/');
    }

    public static function getQuery (): array {
        return $_GET;
    }

    public static function getBody (): array {
        return $_POST;
    }

    public static function getCookies (): array {
        return $_COOKIE;
    }

    /**
     *  @param 'None' | 'Lax' | 'Strict' $samesite
     *  @throws Exception\ClientError When used after some output has already been written.
     */
    public static function setCookie (
        string  $name,
        ?string $value    = null,
        ?int    $expires  = null,
        ?string $path     = null,
        ?string $domain   = null,
        bool    $secure   = false,
        bool    $httponly = false,
        ?string $samesite = null,
    ): void {
        $result = setcookie($name, $value ?? '', Arr::optional([
            'expires'  => $expires,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite
        ]));
        if (!$result)
            throw new Exception\ClientError('Some output already written before setting a cookie.');
    }

    /** @throws Exception\InternalError */
    public static function getHeaders (): array {
        $result = getallheaders();
        if ($result === false)
            throw new Exception\InternalError('Failed to retrieve request headers.');
        return $result;
    }

    public static function setHeader (string $header, bool $replace = true): void {
        header($header, $replace);
    }

    public static function setResponseCode (int $code): void {
        http_response_code($code);
    }

    /**
     *  The currently set response code.
     *  @throws Exception\ClientError When not invoked in web server environment.
     */
    public static function getResponseCode (int $code): int {
        $result = http_response_code();
        if (Check::bool($result))
            throw new Exception\ClientError('Response code is not available outside of a web server.');
        return $result;
    }

}
