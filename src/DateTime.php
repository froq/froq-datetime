<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime;

use froq\datetime\format\{Format, Formatter};
use froq\datetime\locale\{Locale, Intl};
use froq\common\interface\Stringable;

/**
 * An extended `DateTime` class.
 *
 * @package froq\datetime
 * @class   froq\datetime\DateTime
 * @author  Kerem Güneş
 * @since   4.0, 6.0
 */
class DateTime extends \DateTime implements Stringable, \Stringable, \JsonSerializable
{
    /** Default format. */
    public const DEFAULT_FORMAT = 'Y-m-d\TH:i:s.up';

    /** Default locale format. */
    public const DEFAULT_LOCALE_FORMAT = '%d %B %Y, %R';

    /**
     * Constructor.
     *
     * @param  int|float|string|DateTimeInterface|null $when
     * @param  string|DateTimeZone|null                $where
     * @throws froq\datetime\DateTimeException
     */
    public function __construct(int|float|string|\DateTimeInterface $when = null, string|\DateTimeZone $where = null)
    {
        // Now if none.
        $when ??= '';

        // Use when's zone.
        if (is_object($when) && !$where) {
            $where = $when->getTimezone();
        }

        // Use where if non-empty.
        if (is_string($where) && $where) {
            try {
                $where = $this->createTimezone($where);
            } catch (\Throwable $e) {
                throw DateTimeException::forCaughtThrowable($e);
            }
        }

        try {
            // Normal construction.
            if (is_string($when)) {
                parent::__construct($when);
            } else {
                // Extended construction.
                $when = match (get_type($when)) {
                    'int'   => parent::createFromFormat('U', sprintf('%010d', $when)),
                    'float' => parent::createFromFormat('U.u', sprintf('%.6F', $when)),
                    default => parent::createFromInterface($when),
                };

                // With all needed info as format.
                parent::__construct($when->format('Y-m-d H:i:s.u P'));
            }

            // Apply timezone that given or taken from given datetime object.
            // Note: This will override parsed timezone (if $when is string).
            $where && parent::setTimezone($where);
        } catch (\Throwable $e) {
            throw DateTimeException::forCaughtThrowable($e);
        }
    }

    /**
     * @magic
     * @missing
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Set timezone.
     *
     * @param  string|DateTimeZone $timezone
     * @return self
     * @throws froq\datetime\DateTimeException
     * @override
     */
    public function setTimezone(string|\DateTimeZone $timezone): self
    {
        if (is_string($timezone)) {
            try {
                $timezone = $this->createTimezone($timezone);
            } catch (\Throwable $e) {
                throw DateTimeException::forCaughtThrowable($e);
            }
        }

        return parent::setTimezone($timezone);
    }

    /**
     * Get timezone.
     *
     * @return froq\datetime\{DateTimeZone|UtcDateTimeZone}
     * @override
     */
    public function getTimezone(): DateTimeZone|UtcDateTimeZone
    {
        // @tome: Parent's getTimezone() always return DateTimeZone, not a subclass of
        // DateTimeZone (eg: froq\datetime\DateTimeZone or froq\datetime\UtcDateTimeZone).
        return $this->createTimezone(parent::getTimezone()->getName());
    }

    /**
     * Get timezone id.
     *
     * @return string
     */
    public function getTimezoneId(): string
    {
        return $this->getTimezone()->getId();
    }

    /**
     * Get timezone abbr.
     *
     * @return string
     */
    public function getTimezoneAbbr(): string
    {
        return $this->getTimezone()->getAbbr();
    }
    /**
     * Get timezone name.
     *
     * @return string
     */
    public function getTimezoneName(): string
    {
        return $this->getTimezone()->getName();
    }

    /**
     * Set timestamp.
     *
     * @param  int|float $timestamp
     * @return self
     * @override
     */
    public function setTimestamp(int|float $timestamp): self
    {
        return parent::setTimestamp((int) $timestamp);
    }

    /**
     * Get timestamp micros (microtime() style).
     *
     * @return float
     */
    public function getTimestampMicros(): float
    {
        return (float) parent::format('U.u');
    }

    /**
     * Get timestamp millis (JavaScript style).
     *
     * @return float
     */
    public function getTimestampMillis(): int
    {
        return (int) (parent::format('U.u') * 1000);
    }

    /**
     * Get offset code.
     *
     * @return string
     * @missing
     */
    public function getOffsetCode(): string
    {
        return parent::format('P');
    }

    /**
     * Set date.
     *
     * @param  int|string $year
     * @param  int|null   $month
     * @param  int|null   $day
     * @return self
     * @throws froq\datetime\DateTimeException
     * @override
     */
    public function setDate(int|string $year, int $month = null, int $day = null): self
    {
        // Eg: 2022-01-01.
        if (func_num_args() === 1 && is_string($year)) {
            if (!preg_match('~^(\d{4})-(\d{1,2})-(\d{1,2})$~', $year, $match)) {
                throw DateTimeException::forInvalidDate($year);
            }

            [$year, $month, $day] = array_slice($match, 1);
        }

        return parent::setDate((int) $year, (int) $month, (int) $day);
    }

    /**
     * Get date.
     *
     * @return string
     */
    public function getDate(): string
    {
        return parent::format('Y-m-d');
    }

    /**
     * Get full date.
     *
     * @return string
     */
    public function getFullDate(): string
    {
        return parent::format('Y-m-d H:i:s.u');
    }

    /**
     * Set time.
     *
     * @param  int|string $hour
     * @param  int|null   $minute
     * @param  int|null   $second
     * @param  int|null   $microsecond
     * @return self
     * @throws froq\datetime\DateTimeException
     * @override
     */
    public function setTime(int|string $hour, int $minute = null, int $second = null, int $microsecond = null): self
    {
        if (func_num_args() === 1 && is_string($hour)) {
            if (!preg_match('~^(\d{1,2}):(\d{1,2}):(\d{1,2})(?:\.(\d{3,6}))?$~', $hour, $match)) {
                throw DateTimeException::forInvalidTime($hour);
            }

            [$hour, $minute, $second, $microsecond] = array_pad(array_slice($match, 1), 4, null);
        }

        return parent::setTime((int) $hour, (int) $minute, (int) $second, (int) $microsecond);
    }

    /**
     * Get time.
     *
     * @return string
     */
    public function getTime(): string
    {
        return parent::format('H:i');
    }

    /**
     * Get full time.
     *
     * @return string
     */
    public function getFullTime(): string
    {
        return parent::format('H:i:s.u');
    }

    /**
     * Add.
     *
     * @param  int|string|DateInterval $interval
     * @return self
     * @override
     */
    public function add(int|string|\DateInterval $interval): self
    {
        switch (true) {
            case is_int($interval):
                return $this->setTimestamp($this->getTimestamp() + $interval);
            case is_string($interval):
                return $this->setTimestamp($this->getTimestamp() + strtoitime($interval));
            default:
                return parent::add($interval);
        }
    }

    /**
     * Sub.
     *
     * @param  int|string|DateInterval $interval
     * @return self
     * @override
     */
    public function sub(int|string|\DateInterval $interval): self
    {
        switch (true) {
            case is_int($interval):
                return $this->setTimestamp($this->getTimestamp() - $interval);
            case is_string($interval):
                return $this->setTimestamp($this->getTimestamp() - strtoitime($interval));
            default:
                return parent::sub($interval);
        }
    }

    /**
     * Diff.
     *
     * @param  int|string|float|DateTimeInterface $that
     * @param  bool                               $absolute
     * @return froq\datetime\Interval
     * @override
     */
    public function diff(int|float|string|\DateTimeInterface $that, bool $absolute = false): Interval
    {
        if (!$that instanceof \DateTimeInterface) {
            $that = new DateTime($that);
        }

        return new Interval(parent::diff($that, $absolute));
    }

    /**
     * Modify.
     *
     * @param  int|string|DateInterval $modifier
     * @return self
     * @throws froq\datetime\DateTimeException
     * @override
     */
    public function modify(int|string|\DateInterval $modifier): self
    {
        if (is_int($modifier)) {
            $this->setTimestamp($this->getTimestamp() + $modifier);
        } else {
            if ($modifier instanceof \DateInterval) {
                $modifier = $modifier->format(
                    '%r%y year %r%m month %r%d day %r%h hour '.
                    '%r%i minute %r%s second %r%f microsecond'
                );
            }

            if (!@parent::modify($modifier)) {
                $errors = parent::getLastErrors()['errors'] ?? null;
                throw DateTimeException::forFailedModification($errors);
            }
        }

        return $this;
    }

    /**
     * Format.
     *
     * @param  string|froq\datetime\format\Format $format
     * @return string
     * @override
     */
    public function format(string|Format $format): string
    {
        return $this->toString((string) $format);
    }

    /**
     * Format UTC.
     *
     * @param  string|froq\datetime\format\Format $format
     * @return string
     */
    public function formatUtc(string|Format $format): string
    {
        return $this->toUtcString((string) $format);
    }

    /**
     * Format locale.
     *
     * @param  string|froq\datetime\format\Format      $format
     * @param  string|froq\datetime\locale\Locale|null $locale
     * @param  array|froq\datetime\locale\Intl|null    $intl
     * @return string
     */
    public function formatLocale(string|Format $format, string|Locale $locale = null, array|Intl $intl = null): string
    {
        return $this->toLocaleString((string) $format, (string) $locale, (array) $intl);
    }

    /**
     * Format ago.
     *
     * @param  string|froq\datetime\locale\Locale|null $locale
     * @param  array|froq\datetime\locale\Intl|null    $intl
     * @param  string|froq\datetime\format\Format|null $format Used for only more than 7 days.
     * @param  bool                                    $showTime
     * @return string
     */
    public function formatAgo(string|Locale $locale = null, array|Intl $intl = null, string|Format $format = null,
        bool $showTime = true): string
    {
        $formatter = new Formatter(null, $locale, $intl);

        return $formatter->formatAgo($this, $this->getTimezone(), $format, $showTime);
    }

    /**
     * Format by given timezone.
     *
     * @param  string|Format       $format
     * @param  string|DateTimeZone $timezone
     * @return string
     */
    public function formatBy(string|Format $format, string|\DateTimeZone $timezone): string
    {
        return (clone $this)->setTimezone($timezone)->format($format);
    }

    /**
     * @inheritDoc froq\common\interface\Stringable
     *
     * To string (format by given or default format).
     *
     * @param  string|null $format
     * @return string
     */
    public function toString(string $format = null): string
    {
        $formatter = new Formatter($format ?? static::DEFAULT_FORMAT);

        return $formatter->format($this);
    }

    /**
     * To locale string (format by given or default locale format, locale, intl).
     *
     * @param  string|null $format
     * @param  string|null $locale
     * @param  array|null  $intl
     * @return string
     */
    public function toLocaleString(string $format = null, string $locale = null, array $intl = null): string
    {
        $formatter = new Formatter($format ?? static::DEFAULT_LOCALE_FORMAT, $locale, $intl);

        return $formatter->formatLocale($this);
    }

    /**
     * To UTC string (format by UTC timezone, given format or ISO format).
     *
     * @param  string|null $format
     * @return string
     */
    public function toUtcString(string $format = null): string
    {
        $formatter = new Formatter($format ?? Format::ISO_MS);

        return $formatter->formatUtc($this);
    }

    /**
     * To locale UTC string (format by UTC timezone, given or default locale format, locale, intl).
     *
     * @param  string|null $format
     * @param  string|null $locale
     * @param  array|null  $intl
     * @return string
     */
    public function toLocaleUtcString(string $format = null, string $locale = null, array $intl = null): string
    {
        $formatter = new Formatter($format ?? static::DEFAULT_LOCALE_FORMAT, $locale, $intl);

        return $formatter->formatLocaleUtc($this);
    }

    /**
     * To ISO string (format by ISO spec).
     *
     * @return string
     */
    public function toIsoString(): string
    {
        return parent::format(Format::ISO_MS);
    }

    /**
     * To HTTP string (format by HTTP spec).
     *
     * Note: Converts local time to UTC.
     *
     * @return string
     */
    public function toHttpString(): string
    {
        return $this->toUtcString(Format::HTTP);
    }

    /**
     * To HTTP cookie string (format by HTTP cookie spec).
     *
     * Note: Converts local time to UTC.
     *
     * @return string
     */
    public function toHttpCookieString(): string
    {
        return $this->toUtcString(Format::HTTP_COOKIE);
    }

    /**
     * @permissive Return type mixed.
     * @inheritDoc JsonSerializable
     */
    public function jsonSerialize(): mixed
    {
        return (string) $this;
    }

    /**
     * To timestamp.
     *
     * @return froq\datetime\Timestamp
     */
    public function toTimestamp(): Timestamp
    {
        return new Timestamp($this->getTimestamp());
    }

    /**
     * From timestamp.
     *
     * @param  int|float|froq\datetime\Timestamp $timestamp
     * @return froq\datetime\DateTime
     */
    public static function fromTimestamp(int|float|Timestamp $timestamp): DateTime
    {
        return new DateTime(is_number($timestamp) ? $timestamp : $timestamp->getTime());
    }

    /**
     * Parameterized static initializer.
     *
     * @param  int|null                 $year
     * @param  int|null                 $month
     * @param  int|null                 $day
     * @param  int|null                 $hour
     * @param  int|null                 $minute
     * @param  int|null                 $second
     * @param  int|null                 $microsecond
     * @param  string|DateTimeZone|null $timezone
     * @return froq\datetime\DateTime
     */
    public static function of(
        int $year = null, int $month = null, int $day = null,
        int $hour = null, int $minute = null, int $second = null,
        int $microsecond = null, string|\DateTimeZone $timezone = null
    ): DateTime
    {
        $that = new DateTime();

        $that->setDate((int) $year, (int) $month, (int) $day);
        $that->setTime((int) $hour, (int) $minute, (int) $second, (int) $microsecond);

        if ($timezone !== null) {
            $that->setTimezone($timezone);
        }

        return $that;
    }

    /**
     * @internal
     */
    private function createTimezone(string $timezone): UtcDateTimeZone|DateTimeZone
    {
        return ($this instanceof UtcDateTime) ? new UtcDateTimeZone($timezone) : new DateTimeZone($timezone);
    }
}
