<?php declare(strict_types = 1);

namespace Psi\Fragments;

use \Psi\Format;
use \Psi\Util;

class Link {

    static string $quote = '"';

    public static function link (
        string  $content,
        ?string $href   = null,
        ?string $target = null,
        ?string $class  = null
    ): string {
        return Util::captureRender(self::render(...), $content, $href, $target, $class);
    }

    public static function phone (string $phone, ?string $class = null): string {
        return Util::captureRender(self::renderPhone(...), $phone, $class);
    }

    public static function mail (string $mail, ?string $class = null): string {
        return Util::captureRender(self::renderMail(...), $mail, $class);
    }

    public static function render (
        string  $content,
        ?string $href   = null,
        ?string $target = null,
        ?string $class  = null,
    ): void { ?>
        <a <?= self::attr('href', $href) ?> <?= self::attr('target', $target) ?> <?= self::attr('class', $class) ?>>
            <?= $content ?>
        </a>
    <?php }

    public static function renderPhone (string $phone, ?string $class = null): void {
        self::render(Format::phone($phone), href: "tel:$phone", class: $class);
    }

    public static function renderMail (string $mail, ?string $class = null): void {
        self::render($mail, href: "mailto:$mail", class: $class);
    }

    private static function quote (string $s): string {
        // TODO: Escape?
        return self::$quote . $s . self::$quote;
    }

    private static function attr (string $name, ?string $value): string {
        return $value === null ? '' : "$name=" . self::quote($value);
    }

}
