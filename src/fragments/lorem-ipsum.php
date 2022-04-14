<?php declare(strict_types = 1);

namespace Psi\Fragments;

use \Psi\Exception;
use \Psi\Std\Arr;
use \Psi\Util;

class LoremIpsum {

    // TODO: Maybe use some existing API instead.

    /** @var non-empty-list<non-empty-string> */
    protected static array $paragraphs = [
        <<<TXT
            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            Nulla mollis blandit urna, vitae rutrum nulla tempor vel.
            Morbi augue libero, euismod vestibulum libero ac, gravida dictum ipsum.
            Nunc vel suscipit tellus. Vivamus congue quam a porta egestas.
            In vulputate hendrerit justo quis condimentum.
            Nam nulla dolor, consectetur vel tempor et, pulvinar vitae turpis.
            Etiam leo sapien, commodo id aliquam eget, porttitor ac nulla.
            Curabitur orci nisi, viverra sit amet sem ac, consectetur lacinia massa.
            Sed in gravida velit, eu venenatis quam. Nulla dapibus semper eleifend.
            Integer cursus finibus ornare. Phasellus eu ullamcorper ante.
            Praesent in lorem at ante semper scelerisque ut sed lorem.
        TXT,
        <<<TXT
            Maecenas tristique elit purus, vitae faucibus mi facilisis a.
            Suspendisse pulvinar in nisi vel elementum.
            Donec ac nisl quis est scelerisque imperdiet.
            Vivamus lectus erat, eleifend lacinia egestas id, bibendum ut felis.
            Aliquam erat volutpat.
            Proin pellentesque, est ut dictum pharetra, mauris ex molestie tellus, quis sagittis ex velit nec mi.
            Integer ornare ligula sed eros consequat, facilisis feugiat lorem sagittis.
        TXT,
        <<<TXT
            Mauris iaculis varius odio, in posuere sapien.
            Mauris ante nunc, accumsan viverra turpis ac, rhoncus volutpat lorem.
            Proin quis volutpat metus. Nulla non mauris at tellus consectetur egestas.
            Aenean commodo dignissim sodales. In ac tortor vel est feugiat tempus.
            Fusce tincidunt imperdiet elit vestibulum ultricies.
            Mauris tellus orci, semper ac bibendum vel, fringilla a nisl.
            Duis porttitor nisl vitae mi fermentum, a scelerisque erat faucibus.
            Etiam quis lectus eget ligula lacinia sodales et nec odio.
            In molestie velit pulvinar, semper ante a, bibendum tortor.
            Fusce ultricies, risus quis maximus volutpat, mi erat aliquam libero, at faucibus neque est cursus ligula.
        TXT,
        <<<TXT
            Mauris commodo nibh odio, et tincidunt orci hendrerit sit amet.
            Maecenas a convallis nulla, id feugiat orci.
            Maecenas vulputate neque et lorem eleifend consectetur.
            In sit amet mauris hendrerit, posuere lacus quis, blandit lorem.
            Duis placerat lacus lectus, ac vestibulum odio vestibulum nec.
            Maecenas et varius lorem. Vivamus congue ex vitae magna blandit pulvinar.
            Cras malesuada velit non vulputate porttitor.
            Nam magna elit, finibus eget molestie vel, blandit quis massa.
            Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
            Morbi nec sagittis mauris, sit amet vulputate tortor.
            Nunc mattis, justo ut finibus dapibus, orci ipsum lacinia erat, feugiat faucibus est leo et mauris.
            Proin auctor metus nibh, in tempor ante viverra nec.
            Quisque massa lorem, tristique nec luctus nec, congue ac tortor.
            Integer eros mi, blandit eu viverra nec, dictum a quam.
            Praesent viverra faucibus nibh, nec efficitur velit euismod in.
        TXT,
        <<<TXT
            Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae;
            Duis mattis ante quam, a blandit purus convallis id. Donec ut pharetra libero.
            Nam a lectus tempor, suscipit libero molestie, ullamcorper ex.
            Etiam placerat malesuada est id commodo.
            Vivamus lacus lorem, tincidunt vel dui ut, egestas placerat arcu.
            Pellentesque finibus nunc id velit volutpat rutrum.
            Pellentesque nibh orci, porta in ultricies nec, fringilla ac sem.
            Donec aliquet ullamcorper elit, ac accumsan odio. Nulla quis iaculis nunc.
            Sed hendrerit metus a nisl ornare varius. Vivamus sed fringilla massa.
            Praesent tellus velit, vulputate sed felis non, consequat consectetur felis.
            Sed id neque ipsum. Pellentesque ac condimentum sem, eu rhoncus nisi.
        TXT,
    ];

    /** @var non-empty-list<non-empty-string> */
    protected static array $titles = [
        'Lorem Ipsum',
        'Dolor Sit Amet',
        'Mauris Iaculis',
        'Vestibulum Ante Ipsum',
        'Sed id Neque Ipsum',
        'Justo ut Finibus',
        'Eget Ligua Luctus',
    ];

    public static function title (): string {
        return Arr::randomValue(self::$titles);
    }

    public static function htmlTitle (): string {
        return Util::captureRender(self::renderTitle(...), self::title());
    }

    /** @throws Exception\InternalError In case of a possible internal error. */
    public static function sentence (): string {
        $sentences = array_filter(explode('.', Arr::randomValue(self::$paragraphs)));
        if (Arr::empty($sentences))
            throw new Exception\InternalError('No sentences provided.');
        return trim(Arr::randomValue($sentences));
    }

    public static function paragraph (?int $sentences = null): string {
        $content = '';
        if ($sentences === null)
            $content = Arr::randomValue(self::$paragraphs);
        else for ($i = 0; $i < $sentences; ++$i)
            $content .= ' ' . self::sentence() . '.';
        return $content;
    }

    public static function htmlParagraph (?int $sentences = null): string {
        return Util::captureRender(self::renderParagraph(...), self::paragraph($sentences));
    }

    private static function renderParagraph (string $content): void { ?>
        <p>
            <?= $content ?>
        </p>
    <?php }

    private static function renderTitle (string $content): void { ?>
        <h1>
            <?= $content ?>
        </h1>
    <?php }

}
