<?php declare(strict_types = 1);

namespace Psi\Type;

use \Psi\Std\Cast;
use \Psi\Type\NamedType;
use \Psi\Type\StringType;
use \Psi\Type\TypeInterface;

class ArrayKeyType implements TypeInterface {

    public function __construct () {}

    public function __toString (): string {
        return 'array-key';
    }

    public function same (TypeInterface $type): bool {
        return $type instanceof ArrayKeyType;
    }

    public function contains (TypeInterface $type): bool {
        return
            $type->same(new NamedType('int')) ||
            $type->same(new StringType());
    }

    public function check (mixed $value): bool {
        return is_int($value) || is_string($value);
    }

}
