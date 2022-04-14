<?php declare(strict_types = 1);

namespace Psi\Type;

use \Psi\Type\ClassStringType;
use \Psi\Type\TypeInterface;

class StringType implements TypeInterface {

    public function __construct () {}

    public function __toString (): string {
        return 'string';
    }

    public function same (TypeInterface $type): bool {
        return $type instanceof StringType;
    }

    public function contains (TypeInterface $type): bool {
        return
            $this->same($type) ||
            $type instanceof ClassStringType;
    }

    public function check (mixed $value): bool {
        return is_string($value);
    }

}
