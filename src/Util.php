<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

use froq\date\{Date, Diff};
use froq\common\object\StaticClass;
use DateTime;

/**
 * Util.
 *
 * Date/time utilities.
 *
 * @package froq\date
 * @object  froq\date\Util
 * @author  Kerem Güneş
 * @since   4.0
 * @static
 */
final class Util extends StaticClass
{
    /**
     * Ago: get a string representation from given date/time input, optionanlly with given format,
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
        // Both static.
        static $date, $dateNow; if (!$date || !$dateNow) {
            $date    = new DateTime();
            $dateNow = new DateTime();
        }

        // Just update/modify timestamp.
        $date->setTimestamp(Date::init($when)->getTimestamp());

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
                $format ??= ($showTime ? Date::FORMAT_AGO : Date::FORMAT_AGO_SHORT);
                return strftime($format, $date->getTimestamp());

            // Hours, minutes, now.
            default:
                if ($diff->h >= 1) {
                    return $diff->h .' '. (
                        ($diff->h == 1) ? $intl['hour']  ?? 'hour'
                                        : $intl['hours'] ?? 'hours'
                    );
                }

                if ($diff->i >= 1) {
                    return $diff->i .' '. (
                        ($diff->i == 1) ? $intl['minute']  ?? 'minute'
                                        : $intl['minutes'] ?? 'minutes'
                    );
                }

                return $intl['now'] ?? 'Just now'; // A few seconds ago.
        }
    }

    /**
     * Diff: get an array/string representation from given date(s)/time(s) calculating their differences.
     *
     * @param  string|int|float|Date|DateTime      $when1
     * @param  string|int|float|Date|DateTime|null $when2 @default=now
     * @param  string|null                         $format
     * @return array|string
     */
    public static function diff(string|int|float|Date|DateTime $when1,
                                string|int|float|Date|DateTime $when2 = null,
                                string $format = null): array|string
    {
        // When no object given.
        is_object($when1) || $when1 = Date::init($when1);
        is_object($when2) || $when2 = Date::init($when2);

        $when1 = $when1->format(Date::FORMAT_ISO_MS);
        $when2 = $when2->format(Date::FORMAT_ISO_MS);

        $date1 = new DateTime($when1);
        $date2 = new DateTime($when2);

        if ($format != null) {
            return $date1->diff($date2)->format($format);
        }

        $diff = $date1->diff($date2);

        return [
            'dates'       => [$when1, $when2], 'year' => $diff->y, 'month'  => $diff->m, 'day'    => $diff->d,
            'days'        => $diff->days,      'hour' => $diff->h, 'minute' => $diff->i, 'second' => $diff->s,
            'millisecond' => (int) substr((string) $diff->f, 2)
        ];
    }

    /**
     * Diff: get an instance of Diff representation from given date(s)/time(s) calculating their differences.
     *
     * @param  string|int|float|Date|DateTime      $when1
     * @param  string|int|float|Date|DateTime|null $when2 @default=now
     * @return froq\date\Diff
     * @since  5.2
     */
    public static function diffOf(string|int|float|Date|DateTime $when1,
                                  string|int|float|Date|DateTime $when2 = null): Diff
    {
        $diff = self::diff($when1, $when2);

        return new Diff(...$diff);
    }
}
