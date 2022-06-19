<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * An intl translations map / entry class.
 *
 * @package froq\date
 * @object  froq\date\Intl
 * @author  Kerem Güneş
 * @since   6.0
 */
class Intl extends \XArrayObject
{
    /**
     * Constructor.
     *
     * @param array|null $translations
     */
    public function __construct(array $translations = null)
    {
        parent::__construct($translations);
    }

    /**
     * Set translations.
     *
     * @param  array $translations
     * @return void
     */
    public function setTranslations(array $translations): void
    {
        $this->setData($translations);
    }

    /**
     * Get translations.
     *
     * @return array
     */
    public function getTranslations(): array
    {
        return $this->getData();
    }

    /**
     * Add a locale translation.
     *
     * @param  string $locale
     * @param  array  $translation
     * @return self
     */
    public function addTranslation(string $locale, array $translation): self
    {
        $this[$locale] = $translation;

        return $this;
    }

    /**
     * Get a locale translation or return null if not exists.
     *
     * @param  string $locale
     * @return array|null
     */
    public function getTranslation(string $locale): array|null
    {
        return $this[$locale];
    }

    /**
     * Check a locale translation existence.
     *
     * @param  string $locale
     * @return bool
     */
    public function hasTranslation(string $locale): bool
    {
        return $this[$locale] != null;
    }
}
