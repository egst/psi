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

/** @template Controller as object */
class Method extends TargetResolver {

    /** This property can be overriden in a child class. */
    protected static string $defaultMethod = '__index';

    /** @var (callable (array): object) */
    protected mixed $object;
    /** @var (callable (array): string) */
    protected mixed $method;
    /** @var (callable (array): array) */
    protected mixed $args;

    /**
     *  @param (callable (array): object)  $object
     *  @param ?(callable (array): string) $method
     *  @param ?(callable (array): array)  $args
     */
    public final function __construct (
        callable  $object,
        ?callable $method = null,
        ?callable $args   = null
    ) {
        $this->object = $object;
        $this->method = $method ?? fn (array $_): string => static::$defaultMethod;
        $this->args   = $args   ?? fn (array $_): array  => [];
    }

    public function object (array $capture): object {
        return ($this->object)($capture);
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
        $object = $this->object($capture);
        $method = $this->method($capture);
        $args   = $this->args($capture);
        $class  = $object::class;
        if (!is_callable([$object, $method]))
            throw new Exception\RoutingError("The method `$class::$method` is not defined.");
        return function () use ($object, $method, $args, $class) {
            try {
                /** @psalm-suppress MixedMethodCall */
                return $object->$method(...$args);
            } catch (\ArgumentCountError | \TypeError $e) {
                throw new Exception\RoutingError("The method `$class::$method` cannot be called with the given arguments.");
            }
        };

    }

    /**
     *  Only allowing closures here (not string or array callables) to avoid ambiguities.
     *  @param object $class Non-closure object or (Closure (array): string). See the note below.
     *  @param ((Closure (array): string) | string | null) $method
     *  @param ((Closure (array): array)  | array  | null) $args
     *  Note: Since the object type might include a closure,
     *  there is no static check for a correct `$object` parameter type.
     *  It is only used as a function, if it is a closure and it accepts an array.
     *  It is concidered a direct object value otherwise.
     */
    public static function make (
        object                  $object,
        Closure | string | null $method = null,
        Closure | array  | null $args   = null,
    ):  self {
        return new self(
            object: static::normalizeObject($object),
            method: static::normalizeMethod($method),
            args:   static::normalizeArgs($args),
        );
    }

    /**
     *  @return (Closure (array): object)
     */
    private static function normalizeObject (object $matcher): Closure {
        if ($matcher instanceof Closure)
            return function (array $capture) use ($matcher) {
                try {
                    return Cast::object($matcher($capture));
                } catch (\ArgumentCountError | \TypeError $e) {
                    return $matcher;
                }
            };
        return fn (array $_) => $matcher;
    }

    /**
     *  @param ((Closure (array): string) | string | null) $matcher
     *  @return (Closure (array): string)
     */
    private static function normalizeMethod (Closure | string | null $matcher): Closure {
        if ($matcher instanceof Closure)
            return $matcher;
        return fn (array $_) => $matcher ?? static::$defaultMethod;
    }

    /**
     *  @param ((Closure (array): array) | array | null) $matcher
     *  @return (Closure (array): array)
     */
    private static function normalizeArgs (Closure | array | null $matcher): Closure {
        if ($matcher instanceof Closure)
            return $matcher;
        return fn (array $_) => $matcher ?? [];
    }

}
