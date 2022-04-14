<?php declare(strict_types = 1);

namespace Psi\Pattern;

use \Psi\Exception;
use \Psi\Pattern;

class Path implements Pattern {

    public static string $delimiter = '/';

    public function __construct (
        protected string $pattern
    ) {}

    public function regex (): string {
        $pattern = $this->pattern;
        $pattern = str_replace  ('/',             '\/',            $pattern);
        $pattern = preg_replace ('/\*<([^>]+)>/', '(?<$1>[^\/]+)', $pattern) ?? throw new Exception\InternalError('Path regex failed.');
        $pattern = str_replace  ('*',             '([^\/]+)',      $pattern);
        #$pattern = "^$pattern(?:\/|$)";
        $pattern = "^$pattern$";

        return static::$delimiter . $pattern . static::$delimiter;
    }

}
