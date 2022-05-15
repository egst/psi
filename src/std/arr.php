<?php declare(strict_types = 1);

namespace Psi\Std;

class Arr {

    /**
     *  @template T
     *  @param array<T> $a
     *  @param T $e
     */
    public static function contains (array $a, mixed $e): bool {
        return in_array($e, $a);
    }

    /**
     *  Equivalent to count or sizeof.
     *  @return positive-int | 0
     */
    public static function size (array $a): int {
        return count($a);
    }

    /**
     *  Equivalent to array_rand.
     *  @template Key of array-key
     *  @param non-empty-array<Key, mixed> $a
     *  @return Key
     */
    public static function randomKey (array $a): mixed {
        return array_rand($a);
    }

    /**
     *  @template Elem
     *  @param non-empty-array<Elem> $a
     *  @return Elem
     */
    public static function randomValue (array $a): mixed {
        return $a[array_rand($a)];
    }

    /**
     *  Equivalent to array_values.
     *  @template Elem
     *  @param array<Elem> $a
     *  @return list<Elem>
     */
    public static function values (array $a): array {
        return array_values($a);
    }

    /**
     *  Equivalent to array_keys.
     *  @template Key of array-key
     *  @param array<Key, mixed> $a
     *  @return list<Key>
     */
    public static function keys (array $a): array {
        return array_keys($a);
    }

    /**
     *  Like empty, but with tighter type checks.
     *  `empty(null)` is true, while `Arr::empty(null)` is a compile-time error.
     *  @psalm-assert-if-false !empty $a
     */
    public static function empty (array $a): bool {
        return empty($a);
    }

    /**
     *  @template Elem
     *  @param array<Elem> $a
     *  @return ?Elem
     */
    public static function subscript (array $a, int $i): mixed {
        return array_slice($a, $i, 1)[0] ?? null;
    }

    /**
     *  @template Elem
     *  @param array<Elem> $a
     *  @return ?Elem
     */
    public static function last (array $a): mixed {
        return Arr::subscript($a, -1);
    }

    /**
     *  @template Key of array-key
     *  @template Elem
     *  @param array<Key, Elem> $a
     *  @param callable(Elem): bool $f
     *  @return array<Key, Elem>
     */
    public static function filter (array $a, callable $f): array {
        return array_filter($a, $f);
    }

    /**
     *  @template Key of array-key
     *  @template Elem
     *  @param array<Key, Elem> $a
     *  @param callable(Key): bool $f
     *  @return array<Key, Elem>
     */
    public static function filterKeys (array $a, callable $f): array {
        return array_filter($a, $f, ARRAY_FILTER_USE_KEY);
    }

    /**
     *  @template Key of array-key
     *  @template Elem
     *  @param array<Key, Elem> $a
     *  @param callable(Key, Elem): bool $f
     *  @return array<Key, Elem>
     */
    public static function filterPairs (array $a, callable $f): array {
        return array_filter($a, $f, ARRAY_FILTER_USE_BOTH);
    }

    /**
     *  @template K as array-key
     *  @template V
     *  @param array<K, ?V> $a
     *  @return array<K, V>
     */
    public static function optional (array $a): array {
        return array_filter($a, fn (mixed $e) => $e !== null); // TODO: Arr::filter
    }

    /**
     *  @template K as array-key
     *  @template L as array-key
     *  @template V
     *  @param array<K, array<L, V>> $a
     *  @return array<L, array<K, V>>
     */
    public static function transpose (array $a): array {
        $result = [];
        foreach ($a as $i => $entry) foreach ($entry as $j => $val) {
            $result[$j] ??= [];
            $result[$j][$i] = $val;
        }
        return $result;
    }

    /**
     *  Map array values. Keys are preserved.
     *  @template Key of array-key
     *  @template A
     *  @template B
     *  @param array<Key, A> $a
     *  @param callable(A): B $f
     *  @return array<Key, B>
     */
    public static function map (array $a, callable $f): array {
        return array_map($f, $a);
    }

    /**
     *  Map array keys. Values are preserved.
     *  @template A of array-key
     *  @template B of array-key
     *  @template Val
     *  @param array<A, Val> $a
     *  @param callable(A): B $f
     *  @return array<B, Val>
     */
    public static function mapKeys (array $a, callable $f): array {
        return array_combine(array_map($f, Arr::keys($a)), $a);
    }

    /**
     *  Map array keys and values to values only. Keys are preserved.
     *  @template Key of array-key
     *  @template A
     *  @template B
     *  @param array<Key, A> $a
     *  @param callable(Key, A): B $f
     *  @return array<Key, B>
     */
    public static function mapPairs (array $a, callable $f): array {
        $keys = Arr::keys($a);
        return array_combine($keys, array_map($f, $a, $keys));
    }

}
