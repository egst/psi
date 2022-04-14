<?php declare(strict_types = 1);

namespace Psi;

use \Psi\Logger;

class Logger {

    public static function info (string $message, ?string $file = null): void {
        Logger::log(Logger\Level::info, $message, $file);
    }

    public static function warning (string $message, ?string $file = null): void {
        Logger::log(Logger\Level::warning, $message, $file);
    }

    public static function error (string $message, ?string $file = null): void {
        Logger::log(Logger\Level::error, $message, $file);
    }

    /** @return list<Logger\Log> */
    public static function getLogs (): array {
        return Logger::$logs;
    }

    public static function renderConsole (): void { ?>
        <section class="logger">
            <?php foreach (Logger::$logs as $log): ?>
            <?php if ($log->file === null): ?>
            <div class="log <?= $log->type->value ?>">
                <?= $log->message ?>
            </div>
            <?php endif ?>
            <?php endforeach ?>
        </section>
    <?php }

    #public static bool $placed = false;

    public static function write (): void {
        foreach (static::$logs as $log) if ($log->file !== null)
            file_put_contents(static::$logDir . $log->file . '.log', $log->message, FILE_APPEND);
    }

    public static function renderMinimalStylesheet (): void { ?>
        <style>
            body {
                margin-bottom: 150px;
            }
            .logger {
                overflow-y: auto;
                box-sizing: border-box;
                position: fixed;
                z-index: 10000;
                bottom: 0;
                left: 0;
                right: 0;
                border-top: 8px solid #aaa;
                padding: 32px;
                background-color: #444;
                color: white;
                font-size: 12px;
                font-family: 'B612 Mono', monospace;
                height: 150px;
            }
            .logger::before {
                content: 'Psi console:';
                font-weight: bold;
                color: #aaa;
            }
        </style>
    <?php }

    public static string $logDir = 'log/';

    /** @var list<Logger\Log> */
    protected static array $logs = [];

    protected static function log (Logger\Level $type, string $message, ?string $file = null): void {
        Logger::$logs []= new Logger\Log(
            type:    $type,
            file:    $file,
            message: $message
        );
    }

}
