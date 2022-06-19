<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

use DateTime;

/**
 * Date/time utilities.
 *
 * @package froq\date
 * @object  froq\date\DateUtil
 * @author  Kerem Güneş
 * @since   4.0, 6.0
 * @static
 */
final class DateUtil extends \StaticClass
{
    /**
     * Get a string representation from given date/time input, optionanlly with given format,
     * internationalization and showing time when available.
     *
     * @param  string|int|float $when
     * @param  string|null      $format
     * @param  array|nulll      $intl
     * @param  bool             $showTime
     * @return string
     */
    public static function ago(string|int|float $when, string $format = null, array $intl = null,
        bool $showTime = true): string
    {
        static $date, $dateNow;

        $date ??= new DateTime();
        $dateNow ??= new DateTime();

        // Just update/modify timestamp.
        $date->setTimestamp((new Date($when))->getTimestamp());

        $formatter = new Formatter($intl);
        $formatterExec = fn($format) => $formatter->format($date, $format);

        switch ($diff = $dateNow->diff($date)) {
            // Yesterday.
            case ($diff->days == 1):
                $yesterday = $intl['yesterday'] ?? 'Yesterday';
                return $showTime ? $yesterday .', '. $formatterExec('%H:%M') : $yesterday;

            // 2-7 days.
            case ($diff->days >= 2 && $diff->days <= 7):
                return $showTime ? $formatterExec('%A, %H:%M') : $formatterExec('%A');

            // Week & more.
            case ($diff->days > 7):
                return $formatterExec($format ?? ($showTime ? Format::AGO : Format::AGO_SHORT));

            // Hours, minutes, now.
            default:
                if ($diff->h >= 1) {
                    return $diff->h .' '. (
                        $diff->h == 1 ? $intl['hour'] ?? 'hour' : $intl['hours'] ?? 'hours'
                    );
                }

                if ($diff->i >= 1) {
                    return $diff->i .' '. (
                        $diff->i == 1 ? $intl['minute'] ?? 'minute' : $intl['minutes'] ?? 'minutes'
                    );
                }

                return $intl['now'] ?? 'Just now'; // A few seconds ago.
        }
    }

    /**
     * Get a diff/string representation from given date(s)/time(s) calculating their differences.
     *
     * @param  string|int|float|froq\date\Date|DateTime $when1
     * @param  string|int|float|froq\date\Date|DateTime $when2 @default=now
     * @param  string|null                              $format
     * @return string|froq\date\Diff
     */
    public static function diff(string|int|float|Date|DateTime $when1, string|int|float|Date|DateTime $when2 = '',
        string $format = null): string|Diff
    {
        // When no object given.
        is_object($when1) || $when1 = new Date($when1);
        is_object($when2) || $when2 = new Date($when2);

        $date1 = new DateTime($when1->format(Format::ISO_MS));
        $date2 = new DateTime($when2->format(Format::ISO_MS));

        $diff = $date1->diff($date2);

        if ($format) {
            return $diff->format($format);
        }

        return new Diff(
            year: (int) $diff->y, month: (int) $diff->m, day: (int) $diff->d,
            days: (int) $diff->days, hour: (int) $diff->h, minute: (int) $diff->i,
            second: (int) $diff->s, microsecond: (int) substr((string) $diff->f, 2),
        );
    }
}
