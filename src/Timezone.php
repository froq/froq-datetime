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

use froq\date\TimezoneException;
use DateTime, DateTimeZone, Throwable;

/**
 * Timezone.
 * @package froq\date
 * @object  froq\date\Timezone
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 * @static
 */
class Timezone
{
    /**
     * Instance.
     * @var self (static)
     * @since 4.5
     */
    private static self $instance;

    /**
     * Info.
     * @var array
     * @since 4.5
     */
    protected array $info;

    /**
     * Constructor.
     * @param string $id
     * @since 4.5
     */
    public function __construct(string $id)
    {
        $this->info = self::makeInfo($id);
    }

    /**
     * String magic.
     * @return string
     * @since  4.5
     */
    public function __toString()
    {
        return $this->info['id'];
    }

    /**
     * Init.
     * @param  ... $arguments
     * @return self (static)
     * @since  4.0, 4.5 Replaced with make().
     */
    public static final function init(...$arguments): self
    {
        return new static(...$arguments);
    }

    /**
     * Init single.
     * @param  ... $arguments
     * @return self (static)
     * @since  4.5
     */
    public static final function initSingle(...$arguments): self
    {
        return self::$instance ??= new static(...$arguments);
    }

    /**
     * Info.
     * @param  string|null $key
     * @return any
     * @since  4.5
     */
    public final function info(string $key = null)
    {
        return !$key ? $this->info : $this->info[$key] ?? null;
    }

    /**
     * Make.
     * @param  string $id
     * @return DateTimeZone
     * @throws froq\date\TimezoneException
     * @since  4.5 Taken from init().
     */
    public static final function make(string $id): DateTimeZone
    {
        // Validate id & throw a proper message (eg: date_default_timezone_set() notices only).
        if (!self::isValidId($id)) {
            throw new TimezoneException('Invalid timezone id "%s", use UTC, XXX/XXX, ±XX or '.
                '±XX:XX conventions', [$id]);
        }

        try {
            return new DateTimeZone($id);
        } catch (Throwable $e) {
            throw new TimezoneException($e);
        }
    }

    /**
     * Make info.
     * @param  string $id
     * @return array
     * @since  4.5
     */
    public static final function makeInfo(string $id): array
    {
        $zone = self::make($id);
        $date = new DateTime('', $zone);

        $id = $zone->getName();
        $name = str_replace(['/', '_'], [' / ', ' '], $id);
        $transitions = $zone->getTransitions($date->getTimestamp(), $date->getTimestamp());

        return [
            'id' => $id, 'name' => $name,
            'offset' => $date->getOffset(), 'offsetCode' => $date->format('P'),
            'transition' => [
                'date' => $date->format('c'), 'dateUtc' => $transitions[0]['time'],
                'time' => $transitions[0]['ts'], 'utime' => (float) $date->format('U.u'),
                'abbr' => $transitions[0]['abbr'], 'dst' => $transitions[0]['isdst']
            ]
        ];
    }

    /**
     * List.
     * @param  string|int|null $group
     * @param  string|null     $country
     * @return array
     * @throws froq\date\TimezoneException
     */
    public static final function list($group = null, string $country = null): array
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
                        throw new TimezoneException('Invalid group name "%s" given', [$groupName]);
                    }

                    $ids = DateTimeZone::listIdentifiers($group, $country);
                } else {
                    throw new TimezoneException('Invalid group type "%s" given, valids are: int, string, null',
                        [gettype($group)]);
                }
            } else {
                $ids = DateTimeZone::listIdentifiers();
            }
        } catch (Throwable $e) {
            throw new TimezoneException($e);
        }

        $ret = [];
        if ($group == null) { // Always first..
            $ret[] = self::makeInfo('UTC');
        }

        foreach ($ids as $id) {
            if ($group == null && $id == 'UTC') { // Already set first.
                continue;
            }

            $ret[] = self::makeInfo($id);
        }

        return $ret;
    }

    /**
     * List by.
     * @param  string|int  $group
     * @param  string|null $country
     * @return array
     */
    public static final function listBy($group, string $country = null): array
    {
        return self::list($group, $country);
    }

    /**
     * List by country.
     * @param  string|null $country
     * @return array
     */
    public static final function listByCountry($country): array
    {
        return self::list(null, $country);
    }

    /**
     * Is valid id.
     * @param  string $id
     * @return bool
     */
    public static final function isValidId(string $id): bool
    {
        // Eg: "Z" is not valid.
        if (!$id || strlen($id) < 3) {
            return false;
        }

        // Eg: "UTC", "+03", "+03:00" or "Europe/Istanbul".
        if ($id != 'UTC' && !preg_match('~^\w+/\w+|[+-]\d{2}(?:[:]\d{2})?$~', $id)) {
            return false;
        }

        return true;
    }
}

