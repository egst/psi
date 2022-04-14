<?php declare(strict_types = 1);

namespace Psi;

use \Closure;

use \Psi\Exception;
use \Psi\Http;
use \Psi\Http\Method;
use \Psi\Http\Scheme;
use \Psi\Pattern;
use \Psi\Router\CapturingMatch;
use \Psi\Router\ResultHandler;
use \Psi\Router\Route;
use \Psi\Router\Target;
use \Psi\Std\Arr;

/** @template Result */
class Router {

    /** @var list<Route> */
    protected array $routes;

    /** @var ResultHandler<Result> */
    protected ResultHandler $handler;

    /**
     *  @param array<Route>           $routes
     *  @param ?ResultHandler<Result> $handler When not provided, the Result type will be mixed.
     *  The handler can only be set upon construction as it determines the result type statically.
     */
    public function __construct (
        array          $routes  = [],
        ?ResultHandler $handler = null
    ) {
        $this->routes  = Arr::values($routes);
        $this->handler = $handler ?? new ResultHandler;
    }

    /** @return list<Route> */
    public function routes (): array {
        return $this->routes;
    }

    public function route (Route ...$routes): self {
        foreach ($routes as $route)
            $this->routes []= $route;
        return $this;
    }

    /**
     *  This method only resolves to a route target.
     *  To run the target, simply call it - it's a callable that accepts no arguments.
     *
     *  For simple cases, where the route resolution and the target run can be performed at once
     *  and when there is no need for specific failiure handling mechanisms,
     *  use the Router::run method instead.
     *
     *  @return ?(Closure (): Result) Returns null when no route matches the request.
     *  @throws Exception\RoutingError When resolution fails. (E.g. a missing controller method.)
     *  The returned target callable can also throw when its run fails. (E.g. unsupported controller method arguments.)
     */
    public function resolve (bool $throw = false): ?Closure {
        foreach ($this->routes as $route) {
            $target = $route->try(Http\Request::get());
            if ($target !== null)
                return fn () => $this->handler->handle($target());
        }
        return null;
    }

    /**
     *  This method resolves a route target and runs it.
     *  All failures (no match, failed resolution, failed run) are simply reported with exceptions.
     *
     *  For more control over the resolution, use the Router::resolve instead.
     *
     *  @return Result
     *  @throws Exception\RoutingError
     */
    public function run (): mixed {
        try {
            $target = $this->resolve();
        } catch (Exception\RoutingError $e) {
            throw new Exception\RoutingError('Route resolution failed: ' . $e->getMessage());
        }

        if (!$target)
            throw new Exception\RoutingError('No route matches the request.');

        try {
            return $target();
        } catch (Exception\RoutingError $e) {
            throw new Exception\RoutingError('Route target run failed: ' . $e->getMessage());
        }
    }

}

/*

    new Router([
        new Route(
            new RequestMatcher(fn (Http\Request $request) => test($request)),
            new TargetResolver(fn (array        $capture) => resolve($capture['key']))
        ),
        # ...
    ])

*/
