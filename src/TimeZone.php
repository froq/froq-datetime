<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

use DateTime, DateTimeZone;

/**
 * Time zone class with some utility methods.
 *
 * @package froq\date
 * @object  froq\date\TimeZone
 * @author  Kerem Güneş
 * @since   4.0
 */
class TimeZone
{
    /** @const string */
    public const DEFAULT = 'UTC';

    /** @var froq\date\TimeZoneInfo */
    public readonly TimeZoneInfo $info;

    /**
     * Constructor.
     *
     * @param string $id
     * @param bool   $transition
     * @since 4.5
     */
    public function __construct(string $id, bool $transition = false)
    {
        $this->info = self::makeInfo($id, $transition);
    }

    /**
     * @magic
     */
    public function __toString()
    {
        return $this->getId();
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->info->id;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->info->name;
    }

    /**
     * Get offset.
     *
     * @return int
     */
    public function getOffset(): int
    {
        return $this->info->offset;
    }

    /**
     * Get offset code.
     *
     * @return string
     */
    public function getOffsetCode(): string
    {
        return $this->info->offsetCode;
    }

    /**
     * Get transition.
     *
     * @return array|null
     */
    public function getTransition(): array|null
    {
        return $this->info->transition;
    }

    /**
     * Create a DateTimeZone instance or throw a `TimeZoneException` if given ID is invalid.
     *
     * @param  string $id
     * @return DateTimeZone
     * @throws froq\date\TimeZoneException
     * @since  4.5
     */
    public static function make(string $id): DateTimeZone
    {
        // Validate id & throw a proper message (eg: date_default_timezone_set() notices only).
        self::isValidId($id) || throw new TimeZoneException(
            'Invalid time zone id `%s`, use UTC, Xxx/Xxx, ±NN or ±NN:NN convention', $id
        );

        try {
            return new DateTimeZone($id);
        } catch (\Throwable $e) {
            throw new TimeZoneException($e);
        }
    }

    /**
     * Create info data.
     *
     * @param  string $id
     * @param  bool   $transition
     * @return froq\date\TimeZoneInfo
     * @since  4.5
     */
    public static function makeInfo(string $id, bool $transition = false): TimeZoneInfo
    {
        $zone = self::make($id);
        $date = new DateTime('', $zone);

        $id   = $zone->getName();
        $name = str_replace(['/', '_'], [' / ', ' '], $id);

        $info = [
            'id'         => $id,                'name'       => $name,
            'offset'     => $date->getOffset(), 'offsetCode' => $date->format('P'),
            'transition' => null,
        ];

        if ($transition) {
            $transitions = $zone->getTransitions($date->getTimestamp(), $date->getTimestamp());

            $info['transition'] = [
                'date' => $date->format('c'),
                'time' => $transitions[0]['ts'],   'utime' => (float) $date->format('U.u'),
                'abbr' => $transitions[0]['abbr'], 'dst'   => !!$transitions[0]['isdst']
            ];
        }

        return new TimeZoneInfo(...$info);
    }

    /**
     * Check ID validity.
     *
     * @param  string $id
     * @return bool
     */
    public static function isValidId(string $id): bool
    {
        // Eg: "Z" is not valid.
        if (!$id || strlen($id) < 3) {
            return false;
        }

        // Eg: "UTC", "+03", "+03:00" or "Europe/Istanbul".
        if ($id != 'UTC' && !preg_test('~^\w+/\w+|[+-]\d{2}(?:[:]\d{2})?$~', $id)) {
            return false;
        }

        return true;
    }

    /**
     * Set/get default time zone.
     *
     * @param  string|null $id
     * @return string
     * @throws froq\date\TimeZoneException
     */
    public static function default(string $id = null): string
    {
        if ($id !== null && !@date_default_timezone_set($id)) {
            throw new TimeZoneException(new \LastError());
        }

        return date_default_timezone_get() ?: static::DEFAULT;
    }
}
