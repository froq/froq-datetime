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

namespace froq\Date;

use froq\date\Date;
use froq\common\objects\StaticClass;
use DateTime;

/**
 * Util.
 * @package froq\date
 * @object  froq\date\Util
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 * @static
 */
final class Util extends StaticClass
{
    /**
     * Ago.
     * @param  string|int  $when
     * @param  string|null $format
     * @param  array|nulll $intl
     * @param  bool        $showTime
     * @return string
     */
    public static final function ago($when, string $format = null, array $intl = null,
        bool $showTime = true): string
    {
        // Both static.
        static $date, $dateNow; if (!$date || !$dateNow) {
            $date = new DateTime();
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
     * @param  string|int $when
     * @return array
     */
    public static final function diff($when): array
    {
        $date = new DateTime($when = Date::init($when)->format('c'));
        $dateNow = new DateTime();

        $diff = $dateNow->diff($date);

        return ['datetime' => $when, 'year' => $diff->y, 'month' => $diff->m, 'day' => $diff->d,
                'days' => $diff->days, 'hour' => $diff->h, 'minute' => $diff->i, 'second' => $diff->s,
                'millisecond' => $diff->f];
    }
}

