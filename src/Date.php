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
     * Formats.
     * @const string
     */
    public const FORMAT              = 'Y-m-d\TH:i:s', // @default
                 FORMAT_UTC          = 'Y-m-d\TH:i:s\Z',
                 FORMAT_MS           = 'Y-m-d\TH:i:s.u',
                 FORMAT_UTC_MS       = 'Y-m-d\TH:i:s.u\Z',
                 FORMAT_SQL          = 'Y-m-d H:i:s',
                 FORMAT_LOCALE       = '%d %B %Y, %H:%M',
                 FORMAT_LOCALE_SHORT = '%d %B %Y',
                 FORMAT_AGO          = '%d %B %Y, %H:%M',
                 FORMAT_AGO_SHORT    = '%d %B %Y',
                 FORMAT_HTTP         = 'D, d M Y H:i:s \G\M\T', // @rfc7231
                 FORMAT_HTTP_COOKIE  = 'D, d M Y H:i:s \G\M\T'; // @rfc6265


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
     * @param  string|int|null $dateTime
     * @param  string|null     $dateTimeZone
     * @throws froq\date\DateException
     */
    public function __construct($dateTime = null, string $dateTimeZone = null)
    {
        $dateTime = $dateTime ?? '';
        $dateTimeZone = $dateTimeZone ?? date_default_timezone_get();

        try {
            $dateTimeZone = new DateTimeZone($dateTimeZone);

            if (is_string($dateTime)) {
                $dateTime = new DateTime($dateTime, $dateTimeZone);
            } elseif (is_int($dateTime)) {
                $dateTime = (new DateTime('', $dateTimeZone))->setTimestamp($dateTime);
            } else {
                throw new DateException('Invalid date/time type "%s" given, valids are: int, string, null',
                    [gettype($dateTime)]);
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
     * @param  string $timezone
     * @return self (static)
     */
    public final function setTimezone(string $timezone): self
    {
        $this->dateTimeZone = new DateTimeZone($timezone);
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
     * To utc string.
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
     * Ago.
     * @param  int         $timestamp
     * @param  string|null $format
     * @param  array|nulll $intl
     * @param  bool        $showTime
     * @return string
     */
    public static final function ago(int $timestamp, string $format = null, array $intl = null,
        bool $showTime = true): string
    {
        // Both static.
        static $date, $dateNow; if (!$date || !$dateNow) {
            $date = new DateTime();
            $dateNow = new DateTime();
        }

        // Just update/modify timestamp.
        $date->setTimestamp($timestamp);

        switch ($diff = $dateNow->diff($date)) {
            // Yesterday.
            case ($diff->days == 1):
                $yesterday = $intl['yesterday'] ?? 'Yesterday';
                return $showTime ? $yesterday .', '. strftime('%H:%M', $date->getTimestamp())
                                 : $yesterday;

            // 2-7 days.
            case ($diff->days >= 2 && $diff->days <= 7):
                return $showTime ? strftime('%A, %H:%M', $date->getTimestamp())
                                 : strftime('%A', $date->getTimestamp());

            // Week & more.
            case ($diff->days > 7):
                $format = $format ?? ($showTime ? self::FORMAT_AGO : self::FORMAT_AGO_SHORT);
                return strftime($format, $date->getTimestamp());

            // Hours, minutes, now.
            default:
                if ($diff->h >= 1) {
                    return $diff->h .' '. (
                        ($diff->h == 1) ? $intl['hour'] ?? 'hour'
                                        : $intl['hours'] ?? 'hours'
                    );
                }

                if ($diff->i >= 1) {
                    return $diff->i .' '. (
                        ($diff->i == 1) ? $intl['minute'] ?? 'minute'
                                        : $intl['minutes'] ?? 'minutes'
                    );
                }

                return $intl['now'] ?? 'Just now'; // A few seconds ago.
        }
    }

    /**
     * Diff.
     * @param  string|int $dateTime
     * @return array
     */
    public static final function diff($dateTime): array
    {
        $date = new DateTime($dateTime = self::init($dateTime)->format('c'));
        $dateNow = new DateTime();

        $diff = $dateNow->diff($date);

        return ['datetime' => $dateTime, 'year' => $diff->y, 'month' => $diff->m, 'day' => $diff->d,
                'days' => $diff->days, 'hour' => $diff->h, 'minute' => $diff->i, 'second' => $diff->s,
                'millisecond' => $diff->f];
    }

    /**
     * List timezones.
     * @param  string|int  $group
     * @param  string|null $groupCountry
     * @return array
     * @throws froq\date\DateException
     */
    public static final function listTimezones($group = null, string $groupCountry = null): array
    {
        $ret = [];
        if ($group == null) {
            $ret[] = ['id' => 'UTC', 'name' => 'UTC', 'offset' => 0]; // Always first..
        }

        $date = new DateTime();

        if ($group != null) {
            if ($groupCountry != null) { // Eg: tr => TR (for typos).
                $groupCountry = strtoupper($groupCountry);
            }

            if (is_int($group)) {
                $ids = DateTimeZone::listIdentifiers($group, $groupCountry);
            } elseif (is_string($group)) {
                $ids = DateTimeZone::listIdentifiers(constant('DateTimeZone::'. strtoupper($group)), $groupCountry);
            } else {
                throw new DateException('Invalid group type "%s" given, valids are: int, string, null',
                    [gettype($group)]);
            }
        } else {
            $ids = DateTimeZone::listIdentifiers();
        }

        foreach ($ids as $id) {
            if ($group == null && $id == 'UTC') { // Already set first.
                continue;
            }

            $offset = $date->setTimezone(new DateTimeZone($id))->getOffset();
            $offsetString = $date->format('P'); // Eg: +03:00 (for Europe/Istanbul).

            $ret[] = [
                'id' => $id,
                'name' => str_replace(['/', '_'], [' / ', ' '], $id),
                'offset' => $offset,
                'offsetString' => $offsetString
            ];
        }

        return $ret;
    }
}
