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
     *  @param string | array $target
     *  @return ($target is array ? array : mixed)
     */
    public static function replaceVars (string | array $target, array $capture): mixed {
        $variables = Arr::map(Arr::keys($capture), fn ($key) => "\$$key");
        $replacements = Cast::mapString($capture);
        if (is_array($target)) {
            // Ignore non-string array values.
            return Arr::map($target, fn (mixed $value): mixed =>
                /** @var mixed */
                is_string($value) ? self::replace($capture, $variables, $replacements, $value) : $value
            );
        }
        /** @psalm-suppress MixedReturnStatement */
        return self::replace($capture, $variables, $replacements, $target);
    }

    /**
     *  @param array<string> $variables
     *  @param array<string> $replacements
     */
    private static function replace (
        array  $capture,
        array  $variables,
        array  $replacements,
        string $value
    ):  mixed {
        if (str_starts_with($value, '\:'))
            $value = substr($value, 1);
        else if (str_starts_with($value, ':')) {
            $var = substr($value, 1);
            $val = $capture[$var] ?? null;
            if ($val !== null)
                return $val;
        }
        return Str::replace($variables, $replacements, $value);
    }

}
