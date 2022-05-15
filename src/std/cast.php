<?php declare(strict_types = 1);

namespace Psi\Std;

use \Stringable;

use \Psi\Std\Arr;

class Cast {

    // TODO: Functions that take some type info, should take it by the first argument.
    // E.g. Cast::nullableInstance(Foo::class, $value)
    // TODO: Add a generic function that would use the Psi\Type utility.
    // E.g. Cast::to(Type::nullable(Type::nonEmptyString()), $value)

    /**
     *  Null if not an instance of the given class. Keeps null.
     *  @template T of object
     *  @param class-string<T> $class
     *  @return ?T
     */
    public static function nullableInstance (mixed $value, string $class): ?object {
        return ($value === null || !($value instanceof $class)) ? null : $value;
    }

    /**
     *  Null if not a class name. Keeps null.
     *  @return ?class-string
     */
    public static function nullableClassString (mixed $value): ?string {
        return !is_string($value) || !class_exists($value) ? null : $value;
    }

    /**
     *  Converts 'true', 'on' and '1' string values to true.
     *  For other types, the standard bool cast is performed.
     */
    public static function parseBool (mixed $x): bool {
        return is_string($x)
            ? $x === 'true' || $x === 'on' || $x === '1' // Maybe allow (bool) (int) $x?
            : (bool) $x;
    }

    /**
     *  Potentially falsable to nullable.
     *  @template T
     *  @param T | false $x
     *  @return ?T $x
     */
    public static function nullable (mixed $x): mixed {
        return $x === false ? null : $x;
    }

    /** Equivalent to the built-in (bool) cast. */
    public static function bool (mixed $x): bool {
        return (bool) $x;
    }

    /** Casts to bool first when not scalable. */
    public static function int (mixed $x): int {
        return self::scalable($x)
            ? (int) $x
            : (int) (bool) $x;
    }

    /** Casts to bool first when not scalable. */
    public static function float (mixed $x): float {
        return self::scalable($x)
            ? (float) $x
            : (float) (bool) $x;
    }

    public static function nullableNumber (mixed $x): int | float | null {
        if (is_int($x) || is_float($x))
            return $x;
        if (is_numeric($x))
            /** @psalm-suppress InvalidOperand */
            return $x + 0; // This converts it to either an int or a float depending on the string value.
        return null;
    }

    public static function number (mixed $x): int | float {
        return Cast::nullableNumber($x) ?? Cast::int($x);
    }

    /** Returns an empty string when not stringable. */
    public static function string (mixed $x): string {
        return self::stringable($x)
            ? (string) $x
            : '';
    }

    /** Equivalent to the built-in (array) cast. */
    public static function array (mixed $x): array {
        return (array) $x;
    }

    /** Equivalent to the built-in (object) cast. */
    public static function object (mixed $x): object {
        return (object) $x;
    }

    /** Keeps null. */
    public static function nullableBool (mixed $x): ?bool {
        return $x === null ? null : Cast::bool($x);
    }

    /** Keeps null. */
    public static function nullableInt (mixed $x): ?int {
        return $x === null ? null : Cast::int($x);
    }

    /** Keeps null. */
    public static function nullableFloat (mixed $x): ?float {
        return $x === null ? null : Cast::float($x);
    }

    /** Keeps null. */
    public static function nullableString (mixed $x): ?string {
        return $x === null ? null : Cast::string($x);
    }

    /**
     * Keeps null.
     * @return non-empty-string
     */
    public static function nonEmptyString (mixed $x): ?string {
        $s = Cast::string($x);
        return empty($s) ? null : $s;
    }

    /** @return positive-int */
    public static function positiveInt (mixed $x): int {
        $i = Cast::int($x);
        return $i > 1 ? $i : 1;
    }

    /** @return positive-int | 0 */
    public static function nonNegativeInt (mixed $x): int {
        $i = Cast::int($x);
        return $i > 0 ? $i : 0;
    }

    /** Keeps null. */
    public static function nullableObject (mixed $x): ?object {
        return $x === null ? null : (object) $x;
    }

    /** Keeps null. */
    public static function nullableArray (mixed $x): ?array {
        return $x === null ? null : (array) $x;
    }

    /** @return list<bool> */
    public static function mapBool (mixed $a): array {
        return Arr::values(Arr::optional(Arr::map((array) $a, fn (mixed $e) => Cast::nullableBool($e))));
    }

    /** @return list<int> */
    public static function mapInt (mixed $a): array {
        return Arr::values(Arr::optional(Arr::map((array) $a, fn (mixed $e) => Cast::nullableInt($e))));
    }

    /** @return list<float> */
    public static function mapFloat (mixed $a): array {
        return Arr::values(Arr::optional(Arr::map((array) $a, fn (mixed $e) => Cast::nullableFloat($e))));
    }

    /** @return list<string> */
    public static function mapString (mixed $a): array {
        return Arr::values(Arr::optional(Arr::map((array) $a, fn (mixed $e) => Cast::nullableString($e))));
    }

    /** @return list<array> */
    public static function mapArray (mixed $a): array {
        return Arr::values(Arr::Optional(Arr::map((array) $a, fn (mixed $e) => Cast::nullableArray($e))));
    }

    /** @return list<object> */
    public static function mapObject (mixed $a): array {
        return Arr::values(Arr::Optional(Arr::map((array) $a, fn (mixed $e) => Cast::nullableObject($e))));
    }

    private static function stringable (mixed $x): bool {
        return $x === null || is_scalar($x) || $x instanceof Stringable;
    }

    private static function scalable (mixed $x): bool {
        return $x === null || is_scalar($x) || is_array($x);
    }

}
