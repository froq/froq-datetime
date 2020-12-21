<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

namespace froq\Date;

use froq\date\Date;
use froq\common\object\StaticClass;
use DateTime;

/**
 * Util.
 *
 * Date/time utilities.
 *
 * @package froq\date
 * @object  froq\date\Util
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 * @static
 */
final class Util extends StaticClass
{
    /**
     * Ago: get a string representation from given date/time input, optionanlly with given format,
     * internationalization and showing time when available.
     *
     * @param  string|int|float  $when
     * @param  string|null       $format
     * @param  array|nulll       $intl
     * @param  bool              $showTime
     * @return string
     */
    public static final function ago(string|int|float $when, string $format = null, array $intl = null,
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
     * Diff: get an array representation from given date/time calculating differences.
     *
     * @param  string|int|float $when
     * @return array
     */
    public static final function diff(string|int|float $when): array
    {
        $when = Date::init($when)->format('c');

        $date = new DateTime($when);
        $diff = (new DateTime)->diff($date);

        return ['datetime'    => $when,       'year' => $diff->y, 'month'  => $diff->m, 'day'    => $diff->d,
                'days'        => $diff->days, 'hour' => $diff->h, 'minute' => $diff->i, 'second' => $diff->s,
                'millisecond' => $diff->f];
    }
}

