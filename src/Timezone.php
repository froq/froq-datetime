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

use froq\date\DateException;
use DateTime, DateTimeZone, Throwable;

/**
 * Timezone.
 * @package froq\date
 * @object  froq\date\Timezone
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 * @static
 */
final class Timezone
{
    /**
     * Init.
     * @param  string $where
     * @return DateTimeZone
     * @throws froq\date\DateException
     */
    public static function init(string $where): DateTimeZone
    {
        try {
            return new DateTimeZone($where);
        } catch (Throwable $e) {
            throw new DateException($e);
        }
    }

    /**
     * List.
     * @param  string|int|null $group
     * @param  string|null     $country
     * @return array
     * @throws froq\date\DateException
     */
    public static function list($group = null, string $country = null): array
    {
        if ($group == null && $country != null) {
            $group = DateTimeZone::PER_COUNTRY;
        }

        try {
            if ($group != null) {
                if ($country != null) { // Eg: tr => TR (for typos).
                    $country = strtoupper($country);
                }

                if (is_int($group)) {
                    $ids = DateTimeZone::listIdentifiers($group, $country);
                } elseif (is_string($group)) {
                    $group = constant('DateTimeZone::'. ($groupName = strtoupper($group)));
                    if ($group === null) {
                        throw new DateException('Invalid group name "%s" given', [$groupName]);
                    }

                    $ids = DateTimeZone::listIdentifiers($group, $country);
                } else {
                    throw new DateException('Invalid group type "%s" given, valids are: int, string, null',
                        [gettype($group)]);
                }
            } else {
                $ids = DateTimeZone::listIdentifiers();
            }
        } catch (Throwable $e) {
            throw new DateException($e);
        }

        $ret = [];
        if ($group == null) {
            $ret[] = ['id' => 'UTC', 'name' => 'UTC', 'offset' => 0]; // Always first..
        }

        $date = new DateTime();

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

    /**
     * List by.
     * @param  string|int  $group
     * @param  string|null $country
     * @return array
     */
    public static function listBy($group, string $country = null): array
    {
        return self::list($group, $country);
    }

    /**
     * List by country.
     * @param  string|null $country
     * @return array
     */
    public static function listByCountry($country): array
    {
        return self::list(null, $country);
    }

    /**
     * Is valid.
     * @param  string $where
     * @return bool
     */
    public static function isValid(string $where): bool
    {
        try {
            self::init($where);
            return true;
        } catch (DateException $e) {
            return false;
        }
    }
}

