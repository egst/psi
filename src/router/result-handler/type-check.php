<?php declare(strict_types = 1);

namespace Psi\Router\ResultHandler;

use \Psi\Router\ResultHandler;
use \Psi\Exception;
use \Psi\Std\Cast;
use \Psi\Type;

/**
 *  @template T
 *  @extends ResultHandler<T>
 */
class TypeCheck extends ResultHandler {

    /** @param Type<T> $type */
    public function __construct (public Type $type) {
        // TODO: Instead, call parent::__construct.
        // (For some reason Psalm complains about unset properties with that approach.)
        #parent::__construct(...
        $this->handler =
            function (mixed $result) use ($type): mixed {
                if (!$type->check($result)) {
                    $expected = Cast::string($type);
                    $provided = '(' . gettype($result) . ') ' . Cast::string($result);
                    throw new Exception\ClientError("Wrong result type. `$expected` expected but `$provided` provided.");
                }
                return $result;
            }
        ;
    }

}
