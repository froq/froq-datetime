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
     * @param string $info
     */
    public function __construct(string $info)
    {
        $this->info = self::makeInfo($info);
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
     * @return string
     */
    public function getCountry(): string
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
     * @param  string      $country
     * @param  string|null $encoding
     * @return froq\date\Locale
     */
    public static function make(string $language, string $country, string $encoding = null): Locale
    {
        $info = $language . '_' . $country;

        if ($encoding != '') {
            $info .= '.' . $encoding;
        }

        return new Locale($info);
    }

    /**
     * Make a locale info using parsed language, country and encoding parts.
     *
     * @param  string $info
     * @return froq\date\LocaleInfo
     */
    public static function makeInfo(string $info): LocaleInfo
    {
        $info = self::parse($info);

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
        $ret = ['language' => '', 'country' => '', 'encoding' => null];

        if (preg_match('~([a-z]{2})(?:_([a-z-A-Z]{2}))?(?:\.([a-z-A-Z\d\-]+))?~', $info, $match)) {
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
