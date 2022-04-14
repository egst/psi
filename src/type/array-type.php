<?php declare(strict_types = 1);

namespace Psi\Type;

use \Psi\Std\Cast;
use \Psi\Type\ArrayKeyType;
use \Psi\Type\MixedType;
use \Psi\Type\TypeInterface;

class ArrayType implements TypeInterface {

    public function __construct (
        protected TypeInterface $key,
        protected TypeInterface $value,
        protected bool          $list = false
    ) {
        if (!(new ArrayKeyType())->contains($key))
            $this->key = new MixedType();
        if ($list)
            $this->key = new NamedType('int');
    }

    public function __toString (): string {
        return
            ($this->list ? 'list' : 'array') .
            '<' . Cast::string($this->key) . ', ' . Cast::string($this->value) . '>';
    }

    public function same (TypeInterface $type): bool {
        if (!($type instanceof ArrayType))
            return false;
        return
            $this->key->same($type->key)     &&
            $this->value->same($type->value) &&
            $this->list == $type->list;
    }

    public function contains (TypeInterface $type): bool {
        if (!($type instanceof ArrayType))
            return false;
        return
            $this->key->contains($type->key) &&
            $this->value->contains($type->value) &&
            ($this->list ? $type->list : true);
    }

    public function check (mixed $value): bool {
        if (!is_array($value))
            return false;
        $i = 0;
        /** @var mixed $val */
        foreach ($value as $key => $val) {
            if (!$this->key->check($key))
                return false;
            if (!$this->value->check($val))
                return false;
            if ($this->list && $key != $i)
                return false;
            ++$i;
        }
        return true; // Empty array satisfies any array type.
    }

}
