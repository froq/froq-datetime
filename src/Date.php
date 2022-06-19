<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

use froq\common\interface\{Arrayable, Stringable};
use froq\common\trait\FactoryTrait;
use DateTime, DateTimeZone;

/**
 * Date class with some utility methods.
 *
 * @package froq\date
 * @object  froq\date\Date
 * @author  Kerem Güneş
 * @since   4.0
 */
class Date implements Arrayable, Stringable, \JsonSerializable
{
    use FactoryTrait;

    /** @var DateTime */
    protected DateTime $dateTime;

    /** @var DateTimeZone */
    protected DateTimeZone $dateTimeZone;

    /** @var string */
    protected string $format = 'Y-m-d H:i:s.u P';

    /** @var string */
    protected string $locale;

    /** @var string */
    protected string $localeFormat = '%d %B %Y, %R';

    /**
     * Constructor.
     *
     * @param  string|int|float|null $when
     * @param  string|null           $where
     * @param  string|null           $locale
     * @throws froq\date\{TimeZoneException,DateException}
     */
    public function __construct(string|int|float $when = null, string $where = null, string $locale = null)
    {
        $when  ??= '';
        $where ??= TimeZone::default();

        try {
            $dateTimeZone = TimeZone::make($where);
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
        } catch (TimeZoneException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new DateException($e);
        }

        // @cancel: Let user pass proper args.
        // Note: Since DateTime accepts a time zone as first argument ($when), we should make
        // DateTimeZone's same here. Otherwise Date.DateTime & Date.DateTimeZone objects will
        // have different timezones.
        // $zone1 = $dateTime->getTimezone()->getName();
        // $zone2 = $dateTimeZone->getName();
        // if ($zone1 != $zone2) {
        //     $zone = new DateTimeZone($zone1);
        //     $dateTime->setTimezone($zone);
        //     $dateTimeZone = $zone;
        // }

        $this->dateTime     = $dateTime;
        $this->dateTimeZone = $dateTimeZone;
        $this->locale       = $locale ?? Locale::default();
    }

    /**
     * @magic
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Get native "date time" instance.
     *
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }
    /**
     * Get native "date time zone" instance.
     *
     * @return DateTimeZone
     */
    public function getDateTimeZone(): DateTimeZone
    {
        return $this->dateTimeZone;
    }

    /**
     * Set timestamp.
     *
     * @param  int $timestamp
     * @return self
     */
    public function setTimestamp(int $timestamp): self
    {
        $this->dateTime->setTimestamp($timestamp);

        return $this;
    }

    /**
     * Get timestamp.
     *
     * @param  bool $float
     * @return int|float
     */
    public function getTimestamp(bool $float = false): int|float
    {
        return !$float ? $this->dateTime->getTimestamp() : (float) $this->dateTime->format('U.u');
    }

    /**
     * Set timezone.
     *
     * @param  string $where
     * @return self
     */
    public function setTimezone(string $where): self
    {
        $this->dateTimeZone = TimeZone::make($where);
        $this->dateTime->setTimezone($this->dateTimeZone);

        return $this;
    }

    /**
     * Get timezone.
     *
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->dateTimeZone->getName();
    }

    /**
     * Set format.
     *
     * @param  string $format
     * @return self
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format.
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Set locale.
     *
     * @param  string $locale
     * @return self
     * @since  4.5
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     * @since  4.5
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set locale format.
     *
     * @param  string $localeFormat
     * @return self
     * @since  4.0, 4.5
     */
    public function setLocaleFormat(string $localeFormat): self
    {
        $this->localeFormat = $localeFormat;

        return $this;
    }

    /**
     * Get locale format.
     *
     * @return string
     * @since  4.0, 4.5
     */
    public function getLocaleFormat(): string
    {
        return $this->localeFormat;
    }

    /**
     * Get offset from UTC.
     *
     * @param  bool $string
     * @return int|string
     */
    public function offset(bool $string = false): int|string
    {
        return !$string ? $this->dateTime->getOffset() : $this->dateTime->format('P');
    }

    /**
     * Format date.
     *
     * @param  string|null $format
     * @return string
     */
    public function format(string $format = null): string
    {
        return $this->dateTime->format($format ?? $this->format);
    }

    /**
     * Format date by given or default locale.
     *
     * @param  string|null $format
     * @param  string|null $locale
     * @param  array|null  $intl
     * @return string
     */
    public function formatLocale(string $format = null, string $locale = null, array $intl = null): string
    {
        $formatter = new Formatter($intl, $format ?? $this->localeFormat, $locale ?? $this->locale);

        return ($this->offset() <> 0) // UTC check.
             ? $formatter->format($this) : $formatter->formatUtc($this);
    }

    /**
     * Modify this date by given content.
     *
     * @param  string $content
     * @return self
     * @throws froq\date\DateException
     * @since  6.0
     */
    public function modify(string $content): self
    {
        @ $this->dateTime->modify($content) || throw new DateException(
            $this->dateTime->getLastErrors()['errors'][0] ?? 'Failed to modify date'
        );

        return $this;
    }

    /**
     * Compute diff of this & that dates.
     *
     * @param  string|int|float|Date|DateTime $that
     * @return froq\date\Diff
     * @since  6.0
     */
    public function diff(string|int|float|Date|DateTime $that): Diff
    {
        return DateUtil::diff($this, $that);
    }

    /**
     * Create a time zone info on demand.
     *
     * @param  bool $transition
     * @return froq\date\TimeZone
     * @since  6.0
     */
    public function zone(bool $transition = false): TimeZone
    {
        return new TimeZone($this->getTimezone(), $transition);
    }

    /**
     * Alias for getTimestamp().
     *
     * @return int
     */
    public function toInt(): int
    {
        return $this->getTimestamp();
    }

    /**
     * Alias for getTimestamp() with microseconds.
     *
     * @return float
     */
    public function toFloat(): float
    {
        return $this->getTimestamp(true);
    }

    /**
     * @inheritDoc froq\common\interface\Stringable
     * @alias format()
     */
    public function toString(...$args): string
    {
        return $this->format(...$args);
    }

    /**
     * @alias formatLocale()
     */
    public function toLocaleString(...$args): string
    {
        return $this->formatLocale(...$args);
    }

    /**
     * Get date as UTC date string.
     *
     * @param  string|null $format
     * @return string
     */
    public function toUtcString(string $format = null): string
    {
        $date = ($this instanceof UtcDate) ? $this
              : new UtcDate($this->getTimestamp(true));

        return $date->format($format ?? Format::ISO_UTC_MS);
    }

    /**
     * Get date as ISO date string.
     *
     * @return string
     */
    public function toIsoString(): string
    {
        return ($this->offset() <> 0) // UTC check.
             ? $this->format(Format::ISO_MS) : $this->format(Format::ISO_UTC_MS);
    }

    /**
     * Get date as HTTP date string.
     *
     * @return string
     */
    public function toHttpString(): string
    {
        return $this->format(Format::HTTP);
    }

    /**
     * Get date as HTTP-Cookie date string.
     *
     * @return string
     */
    public function toHttpCookieString(): string
    {
        return $this->format(Format::HTTP_COOKIE);
    }

    /**
     * Make a date.
     *
     * @param  int         $year
     * @param  int         $month
     * @param  int         $day
     * @param  int         $hour
     * @param  int         $minute
     * @param  int         $second
     * @param  int         $microsecond
     * @param  string|null $where
     * @param  string|null $locale
     * @return static
     */
    public static function make(
        int $year, int $month = 1, int $day = 1,
        int $hour = 0, int $minute = 0, int $second = 0, int $microsecond = 0,
        string $where = null, string $locale = null): static
    {
        $when = (float) (
            UnixTime::make($year, $month, $day, $hour, $minute, $second)
            . '.' . $microsecond
        );

        return new static($when, $where, $locale);
    }

    /**
     * Alias for toInt() or toString().
     *
     * @param  string|null $format
     * @return int|string
     */
    public static function now(string $format = null): int|string
    {
        $now = new static();

        return !$format ? $now->toInt() : $now->toString($format);
    }

    /**
     * Get an interval from now() date.
     *
     * @param  string   $content
     * @param  int|null $time
     * @return int
     */
    public static function interval(string $content, int $time = null): int
    {
        $time ??= self::now();

        return strtotime($content, $time) - $time;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     * @since 4.5
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
     * @since 4.5
     */
    public function jsonSerialize(): string
    {
        return $this->toIsoString();
    }
}
