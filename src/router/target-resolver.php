<?php declare(strict_types = 1);

namespace Psi\Router;

class TargetResolver {

    /** @param (callable (array): (callable (): mixed)) $resolver */
    public function __construct (public mixed $resolver) {}

    /** @return (callable (): mixed) */
    public function resolve (array $capture): callable {
        return ($this->resolver)($capture);
    }

    public function run (array $capture): mixed {
        return $this->resolve($capture)();
    }

}
