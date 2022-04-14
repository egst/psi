<?php declare(strict_types = 1);

namespace Psi\Router;

class CapturingMatch {

    public function __construct (
        public bool  $match,
        public array $capture = []
    ) {}

    public function update (CapturingMatch $matched): static {
        if (!$this->match)
            return $this;
        $this->match   = $matched->match;
        $this->capture = $matched->capture + $this->capture;
        return $this;
    }

}
