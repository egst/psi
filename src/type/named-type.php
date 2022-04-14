<?php declare(strict_types = 1);

namespace Psi\Type;

use \Psi\Type\TypeInterface;
use \Psi\Std\Cast;

/** 
 *  Atomic types that currently have no dedicated class:
 *  * null, false, and other value types...
 *  * bool
 *  * int
 *  * float
 */
class NamedType implements TypeInterface {

    public function __construct (
        protected string $name
    ) {}

    public function __toString (): string {
        return $this->name;
    }

    public function same (TypeInterface $type): bool {
        return $this->name == Cast::nullableInstance($type, NamedType::class)?->name;
    }

    public function contains (TypeInterface $type): bool {
        return $this->same($type);
    }

    public function check (mixed $value): bool {
        $number = Cast::nullableNumber($value);
        if ($number !== null)
            return $value === $number;
        $class = Cast::nullableClassString($value);
        if ($class !== null)
            return $value instanceof $class;
        return match ($this->name) {
            'bool'  => is_bool($value),
            'int'   => is_int($value),
            'float' => is_float($value),
            'null'  => $value === null,
            'false' => $value === false,
            'true'  => $value === true,
            default => false // TODO: This should be an internal error.
        };
    }

}
