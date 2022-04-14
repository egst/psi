<?php declare(strict_types = 1);

namespace Psi;

use \Psi\Exception;
use \Psi\Std\Cast;

/**
 *  Usage examples:
 *
 *  $translation = Translated::make(english: 'yes', czech: 'ano');
 *  $translation = new Translated(['english' => 'yes', 'czech' => 'ano'])
 *  $translation = (new Translated)->set('english', 'yes')->set('czech', 'ano');
 *
 *  $english = $translation->get('english');
 *  $czech   = $translation->get('czech');
 *  $current = $translation->get(); // Depends on static::current method definition.
 *  $current = (string) $translation;
 */
abstract class Translated {

    /** @param array<string, ?string> $translations */
    public final function __construct (public array $translations = []) {}

    protected function current (): ?string {
        return null;
    }

    public static function make (?string ...$translations): static {
        $translated = new static;
        foreach ($translations as $language => $translation)
            $translated->set(Cast::string($language), Cast::nullableString($translation ?? null));
        return $translated;
    }

    public function set (string $language, ?string $translation): self {
        $this->translations[$language] = $translation;
        return $this;
    }

    public function get (?string $language = null): ?string {
        $language ??= $this->current() ?? throw new Exception\ClientError('No current language set for translated strings.');
        return Cast::nullableString($this->translations[$language] ?? null);
    }

    public function __toString (): string {
        return $this->get() ?? '';
    }

}

