<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

namespace froq\date;

use froq\common\interface\Arrayable;
use froq\date\{Date, UtcDate, DateException, Timezone, TimezoneException};
use DateTime, DateTimeZone, Throwable, JsonSerializable;

/**
 * Date.
 *
 * @package froq\date
 * @object  froq\date\Date
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 */
class Date implements Arrayable, JsonSerializable
{
    /**
     * Intervals.
     * @const int
     */
    public const ONE_MINUTE = 60,
                 ONE_HOUR   = 3600,
                 ONE_DAY    = 86400,
                 ONE_WEEK   = 604800, // 86400 * 7
                 ONE_MONTH  = 2592000, // 86400 * 30
                 ONE_YEAR   = 31536000; // 86400 * 365

    /**
     * Formats.
     * @const string
     */
    public const FORMAT              = 'Y-m-d H:i:s',           // @default
                 FORMAT_TZ           = 'Y-m-d H:i:s P',
                 FORMAT_MS           = 'Y-m-d H:i:s.u',
                 FORMAT_TZ_MS        = 'Y-m-d H:i:s.u P',
                 FORMAT_LOCALE       = '%d %B %Y, %R',
                 FORMAT_LOCALE_SHORT = '%d %B %Y',
                 FORMAT_AGO          = '%d %B %Y, %R',
                 FORMAT_AGO_SHORT    = '%d %B %Y',
                 FORMAT_HTTP         = 'D, d M Y H:i:s \G\M\T', // @rfc7231
                 FORMAT_HTTP_COOKIE  = self::FORMAT_HTTP,       // @rfc6265
                 FORMAT_ISO          = 'Y-m-d\TH:i:sP',
                 FORMAT_ISO_MS       = 'Y-m-d\TH:i:s.uP',
                 FORMAT_ISO_UTC      = 'Y-m-d\TH:i:s\Z',
                 FORMAT_ISO_UTC_MS   = 'Y-m-d\TH:i:s.u\Z',
                 FORMAT_SQL          = self::FORMAT,            // @alias
                 FORMAT_SQL_MS       = self::FORMAT_MS;         // @alias

    /**
     * Instance.
     * @var self (static)
     * @since 4.5
     */
    private static self $instance;

    /**
     * Date time.
     * @var DateTime
     */
    protected DateTime $dateTime;

    /**
     * Date time zone.
     * @var DateTimeZone
     */
    protected DateTimeZone $dateTimeZone;

    /**
     * Format.
     * @var string
     */
    protected string $format = self::FORMAT;

    /**
     * Locale.
     * @var string
     * @since 4.5
     */
    protected string $locale;

    /**
     * Locale format.
     * @var string
     * @since 4.0, 4.5 Renamed from $formatLocale.
     */
    protected string $localeFormat = self::FORMAT_LOCALE;

    /**
     * Constructor.
     * @param  string|int|float|null $when
     * @param  string|null           $where
     * @param  string|null           $locale
     * @throws froq\date\DateException
     */
    public function __construct(string|int|float $when = null, string $where = null, string $locale = null)
    {
        $when  ??= '';
        $where ??= date_default_timezone_get();

        try {
            $dateTimeZone = Timezone::make($where);
            switch (get_type($when)) {
                case 'string': // Eg: 2012-09-12 23:42:53
                    $dateTime = new DateTime($when, $dateTimeZone);
                    break;
                case 'int':    // Eg: 1603339284
                    $dateTime = new DateTime('', $dateTimeZone);
                    $dateTime->setTimestamp($when);
                    break;
                case 'float':  // Eg: 1603339284.221243
                    $dateTime = DateTime::createFromFormat('U.u', sprintf('%.6F', $when));
                    $dateTime->setTimezone($dateTimeZone);
                    break;
            }
        } catch (TimezoneException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DateException($e);
        }

        // @cancel: Let user pass proper args..
        // // Note: Since DateTime accepts a timezone as first argument ($when), we should make
        // // DateTimeZone's same here. Otherwise Date.DateTime & Date.DateTimeZone objects will
        // // have different timezones.
        // $zone1 = $dateTime->getTimezone()->getName();
        // $zone2 = $dateTimeZone->getName();
        // if ($zone1 != $zone2) {
        //     $zone = new DateTimeZone($zone1);
        //     $dateTime->setTimezone($zone);
        //     $dateTimeZone = $zone;
        // }

        $this->dateTime     = $dateTime;
        $this->dateTimeZone = $dateTimeZone;
        $this->locale       = $locale ?? setlocale(LC_TIME, 0) ?: 'en_US.UTF-8';
    }

    /**
     * String magic.
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Init.
     * @param  ... $args
     * @return static
     */
    public static final function init(...$args): static
    {
        return new static(...$args);
    }

    /**
     * Init single.
     * @param  ... $args
     * @return static
     */
    public static final function initSingle(...$args): static
    {
        return self::$instance ??= new static(...$args);
    }

    /**
     * Get date time.
     * @return DateTime
     */
    public final function geDateTime(): DateTime
    {
        return $this->dateTime;
    }
    /**
     * Get date time zone.
     * @return DateTimeZone
     */
    public final function geDateTimeZone(): DateTimeZone
    {
        return $this->dateTimeZone;
    }

    /**
     * Set timestamp.
     * @param  int $timestamp
     * @return self (static)
     */
    public final function setTimestamp(int $timestamp): self
    {
        $this->dateTime->setTimestamp($timestamp);

        return $this;
    }

    /**
     * Get timestamp.
     * @param  bool $float
     * @return int|float
     */
    public final function getTimestamp(bool $float = false)
    {
        return !$float ? $this->dateTime->getTimestamp()
             : (float) $this->dateTime->format('U.u');
    }

    /**
     * Set timezone.
     * @param  string $where
     * @return self (static)
     */
    public final function setTimezone(string $where): self
    {
        $this->dateTimeZone = new DateTimeZone($where);
        $this->dateTime->setTimezone($this->dateTimeZone);

        return $this;
    }

    /**
     * Get timezone.
     * @return string
     */
    public final function getTimezone(): string
    {
        return $this->dateTime->getTimezone()->getName();
    }

    /**
     * Set format.
     * @param  string $format
     * @return self (static)
     */
    public final function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format.
     * @return string
     */
    public final function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Set locale.
     * @param  string $locale
     * @return self
     * @since  4.5
     */
    public final function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     * @return string
     * @since  4.5
     */
    public final function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set locale format.
     * @param  string $localeFormat
     * @return self (static)
     * @since  4.0, 4.5 Renamed from setFormatLocale().
     */
    public final function setLocaleFormat(string $localeFormat): self
    {
        $this->localeFormat = $localeFormat;

        return $this;
    }

    /**
     * Get locale format.
     * @return string
     * @since  4.0, 4.5 Renamed from getFormatLocale().
     */
    public final function getLocaleFormat(): string
    {
        return $this->localeFormat;
    }

    /**
     * Offset.
     * @param  bool $string
     * @return int|string
     */
    public final function offset(bool $string = false)
    {
        return !$string ? $this->dateTime->getOffset()
                        : $this->dateTime->format('P');
    }

    /**
     * Format.
     * @param  string|null $format
     * @return string
     */
    public final function format(string $format = null): string
    {
        return $this->dateTime->format($format ?? $this->format);
    }

    /**
     * Format locale.
     * @param  string|null $format
     * @param  string|null $locale
     * @return string
     */
    public final function formatLocale(string $format = null, string $locale = null): string
    {
        // Memoize current stuff.
        static $currentLocale, $currentTimezone, $timezone;
        $currentLocale   ??= $this->locale;
        $currentTimezone ??= date_default_timezone_get();

        $locale     = $locale ?? $currentLocale;
        $timezone ??= ($currentTimezone != $this->getTimezone()) // Not needed for same timezones.
            ? $this->getTimezone() : null;

        // Locale may be null and was set once by another way (for system-wide usages).
        $locale   && setlocale(LC_TIME, $locale);
        $timezone && date_default_timezone_set($timezone);

        $ret = ($this->offset() != 0) // UTC check.
             ? strftime($format ?? $this->localeFormat, $this->getTimestamp())
             : gmstrftime($format ?? $this->localeFormat, $this->getTimestamp());

        // Restore.
        $locale   && setlocale(LC_TIME, $currentLocale);
        $timezone && date_default_timezone_set($currentTimezone);

        return $ret;
    }

    /**
     * To int.
     * @return int
     */
    public final function toInt(): int
    {
        return $this->getTimestamp();
    }

    /**
     * To float.
     * @return float
     * @since  4.5
     */
    public final function toFloat(): float
    {
        return $this->getTimestamp(true);
    }

    /**
     * To string.
     * @param  string|null $format
     * @return string
     */
    public final function toString(string $format = null): string
    {
        return $this->format($format);
    }

    /**
     * To utc string.
     * @param  string|null $format
     * @return string
     */
    public final function toUtcString(string $format = null): string
    {
        $date = ($this instanceof UtcDate) ? $this
              : new UtcDate($this->getTimestamp(true));

        return $date->format($format ?? self::FORMAT_ISO_UTC_MS);
    }

    /**
     * To iso string.
     * @param  bool $ms
     * @return string
     * @since  4.3
     */
    public final function toIsoString(): string
    {
        return ($this->offset() != 0) // UTC check.
             ? $this->format(self::FORMAT_ISO_MS)
             : $this->format(self::FORMAT_ISO_UTC_MS);
    }

    /**
     * To locale string.
     * @param  string|null $format
     * @return string
     */
    public final function toLocaleString(string $format = null): string
    {
        return $this->formatLocale($format);
    }

    /**
     * To http string.
     * @return string
     */
    public final function toHttpString(): string
    {
        return $this->format(self::FORMAT_HTTP);
    }

    /**
     * To http cookie string.
     * @return string
     */
    public final function toHttpCookieString(): string
    {
        return $this->format(self::FORMAT_HTTP_COOKIE);
    }

    /**
     * Now.
     * @param  string|null $format
     * @return int|string
     */
    public static final function now(string $format = null)
    {
        $now = new static();

        return !$format ? $now->toInt() : $now->toString($format);
    }

    /**
     * Now plus.
     * @param  string      $content
     * @param  string|null $format
     * @return int|string
     * @throws froq\date\DateException
     */
    public static final function nowPlus(string $content, string $format = null)
    {
        $now = new static();

        $now->dateTime->modify($content) || throw new DateException(
            $now->dateTime->getLastErrors()['errors'][0] ?? 'Failed to modify date');

        return !$format ? $now->toInt() : $now->toString($format);
    }

    /**
     * Now minus.
     * @param  string      $content
     * @param  string|null $format
     * @return int|string
     * @throws froq\date\DateException
     */
    public static final function nowMinus(string $content, string $format = null)
    {
        $now = new static();

        $now->dateTime->modify($content) || throw new DateException(
            $now->dateTime->getLastErrors()['errors'][0] ?? 'Failed to modify date');

        return !$format ? $now->toInt() : $now->toString($format);
    }

    /**
     * Interval.
     * @param  string   $content
     * @param  int|null $time
     * @return int
     */
    public static final function interval(string $content, int $time = null): int
    {
        $time ??= self::now();

        return strtotime($content, $time) - $time;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     * @since      4.5
     */
    public function toArray(): array
    {
        return [
            'date'   => $this->toIsoString(),  'dateLocale' => $this->toLocaleString(),
            'time'   => $this->getTimestamp(),      'utime' => $this->getTimestamp(true),
            'offset' => $this->offset(),       'offsetCode' => $this->offset(true),
            'zone'   => $this->getTimezone(),      'locale' => $this->locale,
        ];
    }

    /**
     * @inheritDoc JsonSerializable
     * @since      4.5
     */
    public function jsonSerialize()
    {
        return $this->toIsoString();
    }
}
