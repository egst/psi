<?php declare(strict_types = 1);

namespace Psi\Type;

use \Psi\Std\Cast;
use \Psi\Type\TypeInterface;

/** @template T */
class ClassStringType implements TypeInterface {

    /** @param class-string<T> $class */
    public function __construct (
        public string $class
    ) {}

    public function __toString (): string {
        return 'class-string<' . $this->class . '>';
    }

    public function same (TypeInterface $type): bool {
        return $this->class == Cast::nullableInstance($type, ClassStringType::class)?->class;
    }

    public function contains (TypeInterface $type): bool {
        if (!($type instanceof ClassStringType))
            return false;
        return
            $this->same($type) ||
            is_subclass_of($type->class, $this->class);
    }

    public function check (mixed $value): bool {
        return $this->class === $value; // Is a string and names the same class.
        // This dosen't consider class aliases.
    }

}
