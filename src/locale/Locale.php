<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
declare(strict_types=1);

namespace froq\datetime\locale;

/**
 * A class to use as time locale.
 *
 * @package froq\datetime\locale
 * @class   froq\datetime\locale\Locale
 * @author  Kerem Güneş
 * @since   6.0
 */
class Locale extends \Locale
{
    /** Default time locale. */
    public const DEFAULT = 'en_US.UTF-8';

    /**
     * Parse/validate pattern in parent.
     * @override
     */
    public const PATTERN = '~^
        (?<language>[a-zA-Z]{1,3})
        (?:_(?<country>[a-zA-Z]{2}))?
        (?:.(?<encoding>[a-zA-Z\d\-]+))?
    $~x';

    /**
     * Constructor.
     *
     * @param  string      $language
     * @param  string|null $country
     * @param  string|null $encoding
     * @throws froq\datetime\locale\LocaleException
     * @override
     */
    public function __construct(string $language, string $country = null, string $encoding = null)
    {
        try {
            parent::__construct(
                $language, $country, $encoding,
                category: new \LocaleCategory('time')
            );
        } catch (\LocaleError $e) {
            throw new LocaleException($e);
        }
    }

    /**
     * @throws froq\datetime\locale\LocaleException
     * @override
     */
    public static function from(string $locale): static
    {
        try {
            $info = parent::parse($locale);
            return new static($info['language'], $info['country'], $info['encoding']);
        } catch (\LocaleError $e) {
            throw new LocaleException($e);
        }
    }

    /**
     * @throws froq\datetime\locale\LocaleException
     * @override
     */
    public static function fromTag(string $tag, string $encoding = null): static
    {
        try {
            $info = parent::parseTag($tag);
            return new static($info['language'], $info['country'], $encoding);
        } catch (\LocaleError $e) {
            throw new LocaleException($e);
        }
    }

    /**
     * Set default time locale.
     *
     * @param  string    $locale
     * @param  string ...$locales
     * @return string
     */
    public static function setDefault(string $locale, string ...$locales): string
    {
        return parent::set(LC_TIME, $locale, ...$locales);
    }

    /**
     * Get default time or self default.
     *
     * @param  string $default
     * @return string
     */
    public static function getDefault(string $default = self::DEFAULT): string
    {
        return parent::get(LC_TIME) ?: $default;
    }
}
