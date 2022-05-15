<?php declare(strict_types = 1);

namespace Psi\Std;

use \Closure;

class Check {

    /**
     *  Equivalent to is_bool.
     *  @psalm-assert-if-true bool $x
     */
    public static function bool (mixed $x): bool {
        return is_bool($x);
    }

    /**
     *  Equivalent to is_int.
     *  @psalm-assert-if-true int $x
     */
    public static function int (mixed $x): bool {
        return is_int($x);
    }

    /**
     *  Equivalent to is_float.
     *  @psalm-assert-if-true float $x
     */
    public static function float (mixed $x): bool {
        return is_float($x);
    }

    /**
     *  Check for int or float.
     *  @psalm-assert-if-true number $x
     */
    public static function number (mixed $x): bool {
        return Check::int($x) && Check::float($x);
    }

    /**
     *  Equivalent to is_string.
     *  @psalm-assert-if-true string $x
     */
    public static function string (mixed $x): bool {
        return is_string($x);
    }

    /**
     *  Equivalent to is_array.
     *  @psalm-assert-if-true array $x
     */
    public static function array (mixed $x): bool {
        return is_array($x);
    }

    /**
     *  Equivalent to is_object.
     *  @psalm-assert-if-true object $x
     */
    public static function object (mixed $x): bool {
        return is_object($x);
    }

    /**
     *  Equivalent to is_callable.
     *  @psalm-assert-if-true callable $x
     */
    public static function callable (mixed $x): bool {
        return is_callable($x);
    }

    /**
     *  Check for a callable string, i.e. not a closure and not a callable array.
     *  @psalm-assert-if-true callable-string $x
     */
    public static function callableString (mixed $x): bool {
        return is_callable($x) && is_string($x);
    }

    /**
     *  Check for a callable array.
     *  @psalm-assert-if-true callable-array $x
     *  @-psalm-assert-if-true callable $x
     *  @-psalm-assert-if-true array    $x
     */
    public static function callableArray (mixed $x): bool {
        return is_callable($x) && is_array($x);
    }

    /**
     *  Check for a closure.
     *  @psalm-assert-if-true Closure $x
     */
    public static function closure (mixed $x): bool {
        return $x instanceof Closure;
    }

    /**
     *  Check for a callable representing a function, not a method.
     *  I.e. it's either a callable string or a closure, not a callable array.
     *  @psalm-assert-if-true Closure|callable-string $x
     *  TODO: This assertion doesn't work in the current version of Psalm.
     *  Use `!Check::notFunctionCallable` instead untill they fix it.
     */
    public static function functionCallable (mixed $x): bool {
        return Check::closure($x) || Check::callableString($x);
    }

    /**
     *  Check for a callable representing a function, not a method.
     *  I.e. it's either a callable string or a closure, not a callable array.
     *  @psalm-assert-if-true !Closure         $x
     *  @psalm-assert-if-true !callable-string $x
     *  This is a workaround for a bug in Psalm.
     *  @see Check::functionCallable
     */
    public static function notFunctionCallable (mixed $x): bool {
        return !Check::functionCallable($x);
    }

    /**
     *  Null if not a class name. Keeps null.
     *  @psalm-assert-if-true class-string $x
     */
    public static function classString (mixed $x): bool {
        return is_string($x) && class_exists($x);
    }

}
