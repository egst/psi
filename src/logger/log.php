<?php declare(strict_types = 1);

namespace Psi\Logger;

use \Psi\Logger;

class Log {

    public function __construct (
        public Logger\Level $type,
        public string       $message,
        public ?string      $file
    ) {}

}
