<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

namespace froq\date;

use froq\date\{Date, UtcDate, DateException};
use DateTime, DateTimeZone, Throwable;

/**
 * Date.
 * @package froq\date
 * @object  froq\date\Date
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 */
class Date
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
    public const FORMAT              = 'Y-m-d\TH:i:s', // @default
                 FORMAT_MS           = 'Y-m-d\TH:i:s.u',
                 FORMAT_UTC          = 'Y-m-d\TH:i:s\Z',
                 FORMAT_UTC_MS       = 'Y-m-d\TH:i:s.u\Z',
                 FORMAT_SQL          = 'Y-m-d H:i:s',
                 FORMAT_SQL_MS       = 'Y-m-d H:i:s.u',
                 FORMAT_ISO          = self::FORMAT_UTC_MS, // @alias
                 FORMAT_LOCALE       = '%d %B %Y, %H:%M',
                 FORMAT_LOCALE_SHORT = '%d %B %Y',
                 FORMAT_AGO          = '%d %B %Y, %H:%M',
                 FORMAT_AGO_SHORT    = '%d %B %Y',
                 FORMAT_HTTP         = 'D, d M Y H:i:s \G\M\T', // @rfc7231
                 FORMAT_HTTP_COOKIE  = self::FORMAT_HTTP;       // @rfc6265

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
     * Format locale.
     * @var string
     */
    protected string $formatLocale = self::FORMAT_LOCALE;

    /**
     * Constructor.
     * @param  string|int|null $when
     * @param  string|null     $where
     * @throws froq\date\DateException
     */
    public function __construct($when = null, string $where = null)
    {
        $when = $when ?? '';
        $where = $where ?? date_default_timezone_get();

        try {
            $dateTimeZone = new DateTimeZone($where);

            if (is_string($when)) {
                $dateTime = new DateTime($when, $dateTimeZone);
            } elseif (is_int($when)) {
                $dateTime = (new DateTime('', $dateTimeZone))->setTimestamp($when);
            } else {
                throw new DateException('Invalid date/time type "%s" given, valids are: int, string, null',
                    [gettype($when)]);
            }
        } catch (Throwable $e) {
            throw new DateException($e);
        }

        $this->dateTime = $dateTime;
        $this->dateTimeZone = $dateTimeZone;
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
     * Get date time.
     * @return DateTime
     */
    public final function getDateTime(): DateTime
    {
        return $this->dateTime;
    }
    /**
     * Get date time zone.
     * @return DateTimeZone
     */
    public final function getDateTimeZone(): DateTimeZone
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
     * @return int
     */
    public final function getTimestamp(): int
    {
        return $this->dateTime->getTimestamp();
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
     * Set format locale.
     * @param  string $formatLocale
     * @return self (static)
     */
    public final function setFormatLocale(string $formatLocale): self
    {
        $this->formatLocale = $formatLocale;

        return $this;
    }

    /**
     * Get format locale.
     * @param  bool $short
     * @return string
     */
    public final function getFormatLocale(): string
    {
        return $this->formatLocale;
    }

    /**
     * Get offset.
     * @return int
     */
    public final function getOffset(): int
    {
        return $this->dateTime->getOffset();
    }

    /**
     * Get offset string.
     * @return string
     */
    public final function getOffsetString(): string
    {
        return $this->dateTime->format('P');
    }

    /**
     * Format.
     * @param  string|null $format
     * @return string
     */
    public final function format(string $format = null): string
    {
        return $this->dateTime->format($format ?: $this->getFormat());
    }

    /**
     * Format locale.
     * @param  string|null $format
     * @return string
     */
    public final function formatLocale(string $format = null): string
    {
        return ($this->getOffset() != 0) // UTC check.
            ? strftime($format ?: $this->getFormatLocale(), $this->getTimestamp())
            : gmstrftime($format ?: $this->getFormatLocale(), $this->getTimestamp());
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
     * To string.
     * @param  string|null $format
     * @return string
     */
    public final function toString(string $format = null): string
    {
        return $this->format($format);
    }

    /**
     * To UTC string.
     * @param  string|null $format
     * @return string
     */
    public final function toUtcString(string $format = null): string
    {
        $utc = new Date($this->format(self::FORMAT_MS));
        $utc->setTimezone('UTC');

        return $utc->format($format ?? self::FORMAT_UTC);
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
     * To ISO string.
     * @return string
     * @since  4.3
     */
    public final function toIsoString(): string
    {
        return preg_replace(
            // Get only 3-usec.
            '~(.+)\.(\d{3})(\d+)Z$~', '\1.\2Z',
            $this->toUtcString(self::FORMAT_ISO)
        );
    }

    /**
     * Init.
     * @param  ... $arguments
     * @return self (static)
     */
    public static final function init(...$arguments): self
    {
        return new static(...$arguments);
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

        if (!@$now->dateTime->modify('+'. ltrim($content, '+'))) {
            throw new DateException('@error');
        }

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

        if (!@$now->dateTime->modify('-'. ltrim($content, '-'))) {
            throw new DateException('@error');
        }

        return !$format ? $now->toInt() : $now->toString($format);
    }

    /**
     * Interval.
     * @param  string   $content
     * @param  int|null $time
     * @return int
     */
    public static function interval(string $content, int $time = null): int
    {
        $time = $time ?? static::now();

        return strtotime($content, $time) - $time;
    }
}
