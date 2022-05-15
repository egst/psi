<?php declare(strict_types = 1);

namespace Psi\Router;

use \Closure;

use \Psi\Router\TargetResolver;

class TargetResolver {

    /** @param (callable (array): (callable (): mixed)) $resolver */
    public function __construct (public mixed $resolver) {}

    /** @return (callable (): mixed) */
    public function resolve (array $capture): callable {
        return ($this->resolver)($capture);
    }

    public function run (array $capture): mixed {
        return $this->resolve($capture)();
    }

    public static function direct (Closure $resolver): self {
        return new self(fn (array $_) => $resolver);
    }

    /**
     *  @param object $class Non-closure object or (Closure (array): string).
     *  @param ((Closure (array): string) | string | null) $method
     *  @param ((Closure (array): array)  | array  | null) $args
     *  @see TargetResolver\Method::make(...)
     */
    public static function method ($class, $method = null, $args = null): TargetResolver\Method {
        return TargetResolver\Method::make($class, $method, $args);
    }

    /**
     *  @param ((Closure (array): class-string) | class-string)  $class
     *  @param ((Closure (array): string)       | string | null) $method
     *  @param ((Closure (array): array)        | array  | null) $args
     *  @see TargetResolver\StaticMethod::make(...)
     */
    public static function staticMethod ($class, $method = null, $args = null): TargetResolver\StaticMethod {
        return TargetResolver\StaticMethod::make($class, $method, $args);
    }

}
