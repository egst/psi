<?php declare(strict_types = 1);

namespace Psi\Exception;

use \Exception;

/**
 *  An error caused by access to invalid routes.
 *  Usually this will result in a 404 status
 *  (handled by the user as a reaction to this exception),
 *  but the user is free to handle it differently.
 */
class RoutingError extends Exception {

}
