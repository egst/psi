<?php declare(strict_types = 1);

namespace Psi;

use \ErrorException;
use \Throwable;

use \Psi\Entry;
use \Psi\Layout;
use \Psi\Logger;
use \Psi\Util;

class Init {

    /**
     *  Runing the whole program via this method ensures that:
     *  * All errors are handled as exceptions.
     *  * All uncought exceptions are logged.
     *  * All log entries are written.
     *  @param class-string<Entry>  $entry
     *  @param class-string<Logger> $logger
     *  @param class-string<Layout> $layout
     *  @param bool $console When true (default), a console will be rendered for uncaught exceptions.
     *  Set $console to false in production environment.
     */
    public static function init (
        string $entry,
        string $logger  = Logger::class,
        string $layout  = Layout::class,
        bool   $console = true
    ):  void {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        try {
            $entry::run();

        } catch (Throwable $e) {
            $logger::error((string) $e);
            echo $layout::htmlDocument(
                head: Util::captureRender($logger::renderMinimalStylesheet(...)),
                body: Util::captureRender($logger::renderConsole(...))
            );
        } finally {
            $logger::write();
        }
    }

}
