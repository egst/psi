<?php declare(strict_types = 1);

namespace Psi\Router;

use \Psi\Exception;
use \Psi\Http;
use \Psi\Router\CapturingMatch;
use \Psi\Router\RequestMatcher;
use \Psi\Router\TargetResolver;

/** @psalm-suppress all */
class Route {

    public RequestMatcher $matcher;
    public TargetResolver $resolver;

    public final function __construct (
        RequestMatcher $match,
        TargetResolver $resolve
    ) {
        $this->matcher  = $match;
        $this->resolver = $resolve;
    }

    /** A shortcut for an empty resolver. This route will accept all requests. */
    public static function default (TargetResolver $resolve): static {
        return new static(
            match:   new RequestMatcher,
            resolve: $resolve
        );
    }

    /**
     *  @return ?(callable (): mixed)
     *  @throws Exception\InternalError
     */
    public function try (Http\Request $request): ?callable {
        $matched = $this->matcher->match(Http\Request::get());
        if ($matched->match)
            return $this->resolver->resolve($matched->capture);
        return null;
    }

}
