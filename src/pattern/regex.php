<?php declare(strict_types = 1);

namespace Psi\Pattern;

use \Psi\Pattern;

class Regex implements Pattern {

    public static string $delimiter = '/';

    public function __construct (
        protected string $pattern
    ) {}

    public function regex (): string {
        return static::$delimiter . $this->pattern . static::$delimiter;
    }

}
