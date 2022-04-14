<?php declare(strict_types = 1);

namespace Psi\Router;

use \Closure;

use Psi\Exception;
use Psi\Http;
use Psi\Pattern;
use Psi\Router\CapturingMatch;
use Psi\Std\Cast;

class RequestMatcher {

    /** @var (callable (Http\Request): CapturingMatch) */
    public mixed $matcher;

    /** @param ?(callable (Http\Request): CapturingMatch) $matcher */
    public function __construct (?callable $matcher = null) {
        $this->matcher = $matcher ?? fn (Http\Request $_): CapturingMatch => new CapturingMatch(true);
    }

    public function match (?Http\Request $request = null): CapturingMatch {
        $request ??= Http\Request::get();
        return ($this->matcher)($request);
    }

    /**
     *  TODO: Match headers.
     *  TODO: Match different kinds of POST data.
     *  TODO: What happens when `match` matches no case?
     *  TODO: Case transformations. E.g. `foo/bar-baz` to `Foo::barBaz` or `Foo::bar_baz` or whatever.
     *
     *  @param ((Closure (string):      CapturingMatch) | (Closure (string):      bool) | string | Pattern | null) $path
     *  @param ((Closure (Http\Method): CapturingMatch) | (Closure (Http\Method): bool) | Http\Method      | null) $method
     *  @param ((Closure (Http\Scheme): CapturingMatch) | (Closure (Http\Scheme): bool) | Http\Scheme      | null) $scheme
     *  @param ((Closure (string):      CapturingMatch) | (Closure (string):      bool) | string | Pattern | null) $host
     *  @param ((Closure (int):         CapturingMatch) | (Closure (int):         bool) | int              | null) $port
     *  @param ((Closure (array):       CapturingMatch) | (Closure (array):       bool)                    | null) $query
     *  @param ((Closure (array):       CapturingMatch) | (Closure (array):       bool)                    | null) $body
     *  @param ((Closure (array):       CapturingMatch) | (Closure (array):       bool)                    | null) $headers
     *
     *  @throws Exception\ClientError
     */
    public static function make (
        Closure | string | Pattern | null $path    = null,
        Closure | Http\Method      | null $method  = null,
        Closure | Http\Scheme      | null $scheme  = null,
        Closure | string | Pattern | null $host    = null,
        Closure | int              | null $port    = null,
        Closure                    | null $query   = null,
        Closure                    | null $body    = null,
        Closure                    | null $headers = null
    ):  self {
        return new self(fn (Http\Request $request) =>
            (new CapturingMatch(true))
                ->update(self::normalizePattern($path)($request->path))
                ->update(self::normalizeDirect($method)($request->method))
                ->update(self::normalizeDirect($scheme)($request->scheme))
                ->update(self::normalizePattern($host)($request->host))
                ->update(self::normalizeDirect($port)($request->port))
                ->update(self::normalize($query)($request->query))
                ->update(self::normalize($body)($request->body))
                ->update(self::normalize($headers)($request->headers))
        );
    }

    /**
     *  This handles null matchers and a callable matchers that return a bool.
     *  @param ((Closure (mixed): CapturingMatch) | (Closure (mixed): bool) | null) $matcher
     *  @return (Closure (mixed): CapturingMatch)
     */
    private static function normalize (Closure | null $matcher): Closure {
        if ($matcher === null)
            return fn (mixed $_) => new CapturingMatch(true);
        return function (mixed $value) use ($matcher) {
            $match = $matcher($value);
            return ($match instanceof CapturingMatch)
                ? $match
                : new CapturingMatch($match);
        };
    }

    /**
     *  This performs self::normalize (null, bool return)
     *  and additionally handles direct value matchers.
     *  @psalm-type Direct int | string | Http\Method | Http\Scheme
     *  @param ((Closure (mixed): CapturingMatch) | (Closure (mixed): bool) | Direct | null) $matcher
     *  @return (Closure (mixed): CapturingMatch)
     */
    private static function normalizeDirect (mixed $matcher): Closure {
        if ($matcher === null || $matcher instanceof Closure)
            return self::normalize($matcher);
        return fn (mixed $value) => new CapturingMatch($value == $matcher);
    }

    /**
     *  This performs self::normalizeDirect (null, bool return, direct value)
     *  and additionally handles pattern value matchers.
     *  @param ((Closure (mixed): CapturingMatch) | (Closure (mixed): bool) | string | Pattern | null) $matcher
     *  @return (Closure (mixed): CapturingMatch)
     */
    private static function normalizePattern (mixed $matcher): Closure {
        if (!($matcher instanceof Pattern))
            return self::normalizeDirect($matcher);
        return function (mixed $value) use ($matcher): CapturingMatch {
            $capture = [];
            $match = Cast::bool(preg_match($matcher->regex(), Cast::string($value), $capture));
            return new CapturingMatch($match, $capture);
        };
    }

}
