<?php declare(strict_types = 1);

namespace Psi\Std;

class Str {

    public static function toUpper (string $s): string {
        return strtoupper($s);
    }

    /**
     *  @param non-empty-string $delim
     *  @return non-empty-list<string>
     */
    public static function explode (string $s, string $delim): array {
        return explode($delim, $s);
    }

    /**
     *  Equivalent to str_replace.
     *  Avoids an error in Psalm with the polymorphic string|array return type.
     *
     *  @param string | array<string|int|float> $search
     *  @param string | array<string|int|float> $replace
     *  @param string | array<string|int|float> $subject
     *  @param int $count
     *  @return ($subject is array ? array<string> : string)
     */
    public static function replace (
        string | array $search,
        string | array $replace,
        string | array $subject,
        int &$count = null
    ):  string | array {
        return str_replace($search, $replace, $subject, $count);
    }

    // TODO: Throw or log an error instead of just ignoring failed preg_replace results?

    public static function camel (string $s): string {
        $s = preg_replace_callback('/([^-_ ])[-_ ]+([^-_ ])/', fn ($m) => ($m[1] ?? '') . strtoupper(($m[2] ?? '')), $s);
        $s = preg_replace('/[-_ ]/', '', $s ?? '');
        return $s ?? '';
    }

    public static function pascal (string $s): string {
        $s = Str::camel($s);
        $s = ucfirst($s);
        return $s;
    }

    public static function snake (string $s): string {
        $s = preg_replace_callback('/([^A-Z])([A-Z])/', fn ($m) => ($m[0] ?? '') . '_' . ($m[0] ?? ''), $s);
        $s = preg_replace('/[- ]/', '_', $s ?? '');
        $s = preg_replace_callback('/__([A-Z])/', fn ($m) => '_' . ($m[1] ?? ''), $s ?? '');
        return strtolower($s ?? '');
    }

    public static function kebab (string $s): string {
        $s = preg_replace_callback('/([^A-Z])([A-Z])/', fn ($m) => ($m[1] ?? '') . '-' . ($m[2] ?? ''), $s);
        $s = preg_replace('/[_ ]/', '-', $s ?? '');
        $s = preg_replace_callback('/--([A-Z])/', fn ($m) => '-' . ($m[1] ?? ''), $s ?? '');
        return strtolower($s ?? '');
    }

    public static function words (string $s): string {
        $s = preg_replace_callback('/([^-_ ])[-_ ]+([^-_ ])/', fn ($m) => ($m[1] ?? '') . ' ' . ($m[2] ?? ''), $s);
        $s = preg_replace('/[-_]/', '', $s ?? '');
        return $s ?? '';
    }

    /**
     *  Replace all relevant whitespace characters (`\t\r\n\0`) with spaces (`\s`).
     *  Trim all surrounding whitespace.
     *  Replace repeated spaces with a single space.
     */
    public static function whitespaceCleanup (string $s): string {
        $whitespace = '\t\r\n\0';
        $s = preg_replace("/[$whitespace]/", ' ', $s);
        $s = trim($s ?? '', $whitespace);
        $s = preg_replace('/ +/', ' ', $s);
        return $s ?? '';
    }

    /*
     *  TODO: Check that the following JS functions are rewritten correctly in PHP.
     *  TODO: Check corner cases. They should have been checked with the JS functions, but I'm a bit confused about the missing global flag.
     *  export const camel = s => s
     *      .replace(/([^-_ ])[-_ ]+([^-_ ])/g, (_, m, n) => m + n.toUpperCase())
     *      .replace(/[-_ ]/g, '')
     *  export const snake = s => s
     *      .replace(/([^A-Z])([A-Z])/g, (_, m, n) => m + '_' + n)
     *      .replace(/[- ]/g, '_')
     *      .replace(/__([A-Z])/, (_, m) => '_' + m) // TODO: Why no g?
     *      .toLowerCase()
     *  export const kebab = s => s
     *      .replace(/([^A-Z])([A-Z])/g, (_, m, n) => m + '-' + n)
     *      .replace(/[_ ]/g, '-')
     *      .replace(/--([A-Z])/, (_, m) => '-' + m) // TODO: Why no g?
     *      .toLowerCase()
     *  export const urlFriendly = s => s
     *      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
     *      .replace(/[^A-Za-z0-9_.\-~]/g, '')
     */

}
