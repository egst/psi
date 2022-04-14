<?php declare(strict_types = 1);

namespace Psi\Type;

use \Stringable;

interface TypeInterface extends Stringable {

    public function same (TypeInterface $type): bool;

    public function contains (TypeInterface $type): bool;

    public function check (mixed $value): bool;

}
