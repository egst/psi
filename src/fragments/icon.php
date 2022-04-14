<?php declare(strict_types = 1);

namespace Psi\Fragments;

use \Psi\Util;

class Icon {

    static string $tag   = 'div';
    static string $class = 'icon';

    public static function fontAwesome (string $id): string {
        return Util::captureRender(self::renderFontAwesome(...), $id);
    }

    public static function material (string $id): string {
        return Util::captureRender(self::renderMaterial(...), $id);
    }

    public static function renderFontAwesome (string $id): void { ?>
        <<?= self::$tag ?> class="<?= self::$class ?> <?= $id ?>"></<?= self::$tag ?>>
    <?php }

    public static function renderMaterial (string $id, ?string $tag = null): void { ?>
        <<?= self::$tag ?> class="<?= self::$class ?> material-icons"><?= $id ?></<?= self::$tag ?>>
    <?php }

}
