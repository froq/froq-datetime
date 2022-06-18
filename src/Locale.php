<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * A class for locale time stuff.
 *
 * @package froq\date
 * @object  froq\date\Locale
 * @author  Kerem Güneş
 * @since   6.0
 */
class Locale
{
    /** @const int */
    public const DEFAULT = 'en_US.UTF-8';

    /** Parsed locale info. */
    public readonly LocaleInfo $info;

    /**
     * Constructor.
     *
     * @param string $locale
     */
    public function __construct(string $locale)
    {
        $this->info = self::makeInfo($locale);
    }

    /**
     * @magic
     */
    public function __toString()
    {
        $ret = $this->info->language;

        if ($this->info->country) {
            $ret .= '_' . $this->info->country;
        }
        if ($this->info->encoding) {
            $ret .= '.' . $this->info->encoding;
        }

        return $ret;
    }

    /**
     * Get language.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->info->language;
    }

    /**
     * Get country.
     *
     * @return string|null
     */
    public function getCountry(): string|null
    {
        return $this->info->country;
    }

    /**
     * Get encoding.
     *
     * @return string|null
     */
    public function getEncoding(): string|null
    {
        return $this->info->encoding;
    }

    /**
     * Make a locale using language, country and encoding params.
     *
     * @param  string      $language
     * @param  string|null $country
     * @param  string|null $encoding
     * @return froq\date\Locale
     */
    public static function make(string $language, string $country = null, string $encoding = null): Locale
    {
        $info = $language;

        if ($country != '') {
            $info .= '_' . $country;
        }
        if ($encoding != '') {
            $info .= '.' . $encoding;
        }

        return new Locale($info);
    }

    /**
     * Make a locale info using parsed language, country and encoding parts.
     *
     * @param  string $locale
     * @return froq\date\LocaleInfo
     */
    public static function makeInfo(string $locale): LocaleInfo
    {
        if (trim($locale) == '') {
            throw new LocaleException('Empty locale given');
        }

        $info = self::parse($locale);

        if (empty($info['language'])) {
            throw new LocaleException('Invalid locale: ' . $locale);
        }

        return new LocaleInfo(...$info);
    }

    /**
     * Parse locale info to language, country and encoding parts.
     *
     * @param  string $info
     * @return froq\date\LocaleInfo
     */
    public static function parse(string $info): array
    {
        $ret = ['language' => '', 'country' => null, 'encoding' => null];

        // Expected format: language[_COUNTRY[.encoding]]
        if (preg_match('~^([a-z]{2,3})(?:_([A-Z]{2}))?(?:.([a-z-A-Z\d\-]+))?$~', $info, $match)) {
            $ret = array_combine(array_keys($ret), array_pad(array_slice($match, 1), 3, null));
        }

        return $ret;
    }

    /**
     * Get LC_TIME info or default.
     *
     * @param  string|null $default
     * @return string
     */
    public static function default(string $default = null): string
    {
        return getlocale(LC_TIME, default: $default ?? static::DEFAULT);
    }
}
