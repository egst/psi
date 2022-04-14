<?php declare(strict_types = 1);

namespace Psi\Type;

use \Psi\Type\TypeInterface;

class MixedType implements TypeInterface {

    public function __construct () {}

    public function __toString (): string {
        return 'mixed';
    }

    public function same (TypeInterface $type): bool {
        return $type instanceof MixedType;
    }

    public function contains (TypeInterface $type): bool {
        return true;
    }

    public function check (mixed $value): bool {
        return true;
    }

}
