<?php declare(strict_types = 1);

namespace Psi;

use \Psi\Exception;
use \Psi\Std\Arr;
use \Psi\Std\Cast;
use \Psi\Std\Str;

/**
 *  Unsorted misc. utilities.
 */
class Util {

    /**
     *  This function is not fully type-safe.
     *  Any type and ammount of arguments can currently
     *  be passed without a compile-time error. (TODO)
     *  @-param (callable (...): void) $renderer
     */
    public static function captureRender (callable $renderer, mixed ...$args): string {
        ob_start();
        $renderer(...$args);
        return Cast::nullable(ob_get_clean()) ?? throw new Exception\InternalError('Output buffering fail.');
    }

    /**
     *  @template E
     *  @template T as string | array<E>
     *  @param T $target
     *  @return (T is array ? array<E> : string)
     */
    public static function replaceVars (string | array $target, array $capture): string | array {
        $search  = Arr::map(Arr::keys($capture), fn ($key) => "\$$key");
        $replace = Cast::mapString($capture);
        if (is_array($target)) {
            // Ignore non-string array values.
            return Arr::map($target, fn (mixed $value): mixed =>
                /** @var mixed */
                is_string($value)
                    ? Str::replace($search, $replace, $value)
                    : $value
            );
        }
        return Str::replace($search, $replace, $target);
    }

}
