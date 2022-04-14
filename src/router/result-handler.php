<?php declare(strict_types = 1);

namespace Psi\Router;

/** @template T */
class ResultHandler {

    /** @var (callable (mixed): T) */
    public mixed $handler;

    /** @param ?(callable (mixed): T) $handler */
    public function __construct (?callable $handler = null) {
        $this->handler = $handler ?? fn (mixed $result): mixed => $result;
    }

    /** @return T */
    public function handle (mixed $result): mixed {
        return ($this->handler)($result);
    }

}
