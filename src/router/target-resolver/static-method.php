<?php declare(strict_types = 1);

namespace Psi\Router\TargetResolver;

use \Closure;
use \ReflectionMethod;
use \ReflectionType;

use \Psi\Exception;
use \Psi\Router\TargetResolver;
use \Psi\Std\Arr;
use \Psi\Std\Cast;
use \Psi\Std\Check;
use \Psi\Std\Str;
use \Psi\Type;
use \Psi\Util;

class StaticMethod extends TargetResolver {

    /** This property can be overriden in a child class. */
    protected static string $defaultMethod = '__index';

    /** @var (callable (array): class-string) */
    protected mixed $class;
    /** @var (callable (array): string) */
    protected mixed $method;
    /** @var (callable (array): array) */
    protected mixed $args;

    /**
     *  @param (callable (array): class-string) $class
     *  @param ?(callable (array): string)                  $method
     *  @param ?(callable (array): array)                   $args
     */
    public final function __construct (
        callable  $class,
        ?callable $method = null,
        ?callable $args   = null
    ) {
        $this->class  = $class;
        $this->method = $method ?? fn (array $_): string => static::$defaultMethod;
        $this->args   = $args   ?? fn (array $_): array  => [];
    }

    /** @return class-string */
    public function class (array $capture): string {
        return ($this->class)($capture);
    }

    public function method (array $capture): string {
        return ($this->method)($capture);
    }

    public function args (array $capture): array {
        return ($this->args)($capture);
    }

    /**
     *  @throws Exception\RoutingError When the arguments are incompatible or the method is not defined.
     *  @return (callable (): mixed)
     */
    public function resolve (array $capture): callable {
        $class  = $this->class($capture);
        $method = $this->method($capture);
        $args   = $this->args($capture);
        if (!is_callable([$class, $method]))
            throw new Exception\RoutingError("The static method `$class::$method` is not defined.");
        return function () use ($class, $method, $args) {
            try {
                /** @psalm-suppress MixedMethodCall */
                return $class::$method(...$args);
            } catch (\ArgumentCountError | \TypeError $e) {
                throw new Exception\RoutingError("The static method `$class::$method` cannot be called with the given arguments.");
            }
        };
    }

    /**
     *  Only allowing closures here (not string or array callables) to avoid ambiguities.
     *  @param ((Closure (array): class-string) | class-string)  $class
     *  @param ((Closure (array): string)       | string | null) $method
     *  @param ((Closure (array): array)        | array  | null) $args
     *
     *  TODO: Capture values in strings.
     *  E.g.: `method: 'request_$sub'` instead of `method: fn ($capture) => "method_$capture[sub]"`
     */
    public static function make (
        Closure | string        $class,
        Closure | string | null $method = null,
        Closure | array  | null $args   = null,
    ):  self {
        return new self(
            class:  static::normalizeClass($class),
            method: static::normalizeMethod($method),
            args:   static::normalizeArgs($args),
        );
    }

    /**
     *  @param ((Closure (mixed): class-string) | class-string) $matcher
     *  @return (Closure (mixed): class-string)
     */
    private static function normalizeClass (Closure | string $matcher): Closure {
        if ($matcher instanceof Closure)
            return $matcher;
        return fn (mixed $_) => $matcher;
    }

    /**
     *  @param ((Closure (mixed): string) | string | null) $matcher
     *  @return (Closure (mixed): string)
     */
    private static function normalizeMethod (Closure | string | null $matcher): Closure {
        if ($matcher instanceof Closure)
            return $matcher;
        return fn (mixed $_) => $matcher ?? static::$defaultMethod;
    }

    /**
     *  @param ((Closure (mixed): array) | array | null) $matcher
     *  @return (Closure (mixed): array)
     */
    private static function normalizeArgs (Closure | array | null $matcher): Closure {
        if ($matcher instanceof Closure)
            return $matcher;
        return fn (mixed $_) => $matcher ?? [];
    }

}
