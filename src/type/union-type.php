<?php declare(strict_types = 1);

namespace Psi\Type;

use \Psi\Type\TypeInterface;
use \Psi\Std\Cast;

class UnionType implements TypeInterface {

    public function __construct (
        protected TypeInterface $first,
        protected TypeInterface $second
    ) {}

    public function __toString (): string {
        return Cast::string($this->first) . ' | ' . Cast::string($this->second);
    }

    public function same (TypeInterface $type): bool {
        if (!($type instanceof UnionType))
            return false;
        return
            ($this->first == $type->first  && $this->second == $type->second) ||
            ($this->first == $type->second && $this->second == $type->first);
    }

    public function contains (TypeInterface $type): bool {
        return
            $this->first->contains($type) ||
            $this->second->contains($type);
    }

    public function check (mixed $value): bool {
        return
            $this->first->check($value) ||
            $this->second->check($value);
    }

}
