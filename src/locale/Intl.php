<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
declare(strict_types=1);

namespace froq\datetime\locale;

/**
 * A simple internationalization (translation) map / entry class.
 *
 * @package froq\datetime\locale
 * @object  froq\datetime\locale\Intl
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
     * @return self
     */
    public function setTranslations(array $translations): self
    {
        $this->setData($translations);

        return $this;
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
    public function setTranslation(string $locale, array $translation): self
    {
        $this[$locale] = $translation;

        return $this;
    }

    /**
     * Get a locale translation.
     *
     * @param  string $locale
     * @return array|null
     */
    public function getTranslation(string $locale): array|null
    {
        return $this[$locale];
    }

    /**
     * Check a locale translation.
     *
     * @param  string $locale
     * @return bool
     */
    public function hasTranslation(string $locale): bool
    {
        return $this[$locale] != null;
    }

    /**
     * Set days (day names) for a locale.
     *
     * @param  string $locale
     * @param  array  $days
     * @return self
     */
    public function setDays(string $locale, array $days): self
    {
        $this[$locale] = [...(array) $this[$locale], ...['days' => $days]];

        return $this;
    }

    /**
     * Get days (day names) for a locale.
     *
     * @return array|null
     */
    public function getDays(string $locale): array|null
    {
        return $this[$locale]['days'] ?? null;
    }

    /**
     * Set months (month names) for a locale.
     *
     * @param  string $locale
     * @param  array  $months
     * @return self
     */
    public function setMonths(string $locale, array $months): self
    {
        $this[$locale] = [...(array) $this[$locale], ...['months' => $months]];

        return $this;
    }

    /**
     * Get months (month names) for a locale.
     *
     * @return array|null
     */
    public function getMonths(string $locale): array|null
    {
        return $this[$locale]['months'] ?? null;
    }

    /**
     * Set periods (am/pm) for a locale.
     *
     * @param  string $locale
     * @param  array  $periods
     * @return self
     */
    public function setPeriods(string $locale, array $periods): self
    {
        $this[$locale] = [...(array) $this[$locale], ...['periods' => $periods]];

        return $this;
    }

    /**
     * Get periods (am/pm) for a locale.
     *
     * @return array|null
     */
    public function getPeriods(string $locale): array|null
    {
        return $this[$locale]['periods'] ?? null;
    }
}
