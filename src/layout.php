<?php declare(strict_types = 1);

namespace Psi;

use \Psi\Util;

class Layout {

    public static function htmlDocument (
        string $body           = '',
        string $head           = '',
        string $title          = '',
        string $bodyClass      = '',
        string $bodyAttributes = ''
    ):  string {
        return Util::captureRender(static::renderHtmlDocument(...), $body, $head, $title, $bodyClass);
    }

    public static function renderHtmlDocument (
        string $body           = '',
        string $head           = '',
        string $title          = '',
        string $bodyClass      = '',
        string $bodyAttributes = ''
    ):  void { ?>
        <!DOCTYPE html>
        <html>
            <head>
                <? // TODO: Meta charset should be placed before the title? ?>
                <title><?= $title ?></title>
                <?php // CSS Defaults: ?>
                <style>
                    body {
                        margin: 0;
                    }
                </style>
                <?= $head ?>
            </head>
            <body class="<?= $bodyClass ?>" <?= $bodyAttributes ?>>
                <?= $body ?>
            </body>
        </html>
    <?php }

}
