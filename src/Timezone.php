<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

use froq\date\TimezoneException;
use froq\common\trait\FactoryTrait;
use Throwable, DateTime, DateTimeZone;

/**
 * Timezone.
 *
 * Represents an extended timezone entity with some utility methods.
 *
 * @package froq\date
 * @object  froq\date\Timezone
 * @author  Kerem Güneş
 * @since   4.0
 */
class Timezone
{
    /**
     * @see froq\common\trait\FactoryTrait
     * @since 5.0
     */
    use FactoryTrait;

    /** @var array @since 4.5 */
    protected array $info;

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
     * Magic - string: returns "id" field from info stack.
     *
     * @return string
     * @since  4.5
     */
    public function __toString()
    {
        return $this->info['id'];
    }

    /**
     * Get info stack or only one field with given key.
     *
     * @param  string|null $key
     * @return any
     * @since  4.5
     */
    public final function info(string $key = null)
    {
        return !$key ? $this->info : $this->info[$key] ?? null;
    }

    /**
     * Create a DateTimeZone instance or throw a `TimezoneException` if an invalid id given.
     *
     * @param  string $id
     * @return DateTimeZone
     * @throws froq\date\TimezoneException
     * @since  4.5 Taken from init().
     */
    public static final function make(string $id): DateTimeZone
    {
        // Validate id & throw a proper message (eg: date_default_timezone_set() notices only).
        self::isValidId($id) || throw new TimezoneException(
            'Invalid timezone id `%s`, use UTC, Xxx/Xxx, ±NN or ±NN:NN convention', $id
        );

        try {
            return new DateTimeZone($id);
        } catch (Throwable $e) {
            throw new TimezoneException($e);
        }
    }

    /**
     * Create info stack.
     *
     * @param  string $id
     * @param  bool   $transition
     * @return array
     * @since  4.5
     */
    public static final function makeInfo(string $id, bool $transition = false): array
    {
        $zone = self::make($id);
        $date = new DateTime('', $zone);

        $id   = $zone->getName();
        $name = str_replace(['/', '_'], [' / ', ' '], $id);

        $info = [
            'id'     => $id,                'name'       => $name,
            'offset' => $date->getOffset(), 'offsetCode' => $date->format('P'),
        ];

        if ($transition) {
            $transitions = $zone->getTransitions($date->getTimestamp(), $date->getTimestamp());

            $info['transition'] = [
                'date' => $date->format('c'),
                'time' => $transitions[0]['ts'],   'utime' => (float) $date->format('U.u'),
                'abbr' => $transitions[0]['abbr'], 'dst'   => !!$transitions[0]['isdst']
            ];
        }

        return $info;
    }

    /**
     * List identifiers.
     *
     * @param  string|int|null $group
     * @param  string|null     $country
     * @param  bool            $transition
     * @return array
     * @throws froq\date\TimezoneException
     */
    public static final function list(string|int $group = null, string $country = null, bool $transition = false): array
    {
        if ($group == null && $country != null) {
            $group = DateTimeZone::PER_COUNTRY;
        }

        try {
            if ($group != null) {
                // Eg: tr => TR (for typos).
                $country && $country = strtoupper($country);

                if (is_string($group)) {
                    $constant = 'DateTimeZone::'. strtoupper($group);
                    defined($constant) || throw new TimezoneException(
                        'Invalid group %s, use a valid DateTimeZone constant name', $group
                    );

                    $ids = DateTimeZone::listIdentifiers(constant($constant), $country);
                } else {
                    $ids = DateTimeZone::listIdentifiers($group, $country);
                }
            } else {
                $ids = DateTimeZone::listIdentifiers();
            }
        } catch (Throwable $e) {
            throw new TimezoneException($e);
        }

        $ret = [];

        if ($group == null) { // Always first..
            $ret[] = self::makeInfo('UTC', $transition);
        }

        foreach ($ids as $id) {
            if ($group == null && $id == 'UTC') { // Already set first.
                continue;
            }

            $ret[] = self::makeInfo($id, $transition);
        }

        return $ret;
    }

    /**
     * List identifiers by given group.
     *
     * @param  string|int $group
     * @param  bool       $transition
     * @return array
     */
    public static final function listByGroup(string|int $group, bool $transition = false): array
    {
        return self::list($group, null, $transition);
    }

    /**
     * List identifiers by given country.
     *
     * @param  string $country
     * @param  bool   $transition
     * @return array
     */
    public static final function listByCountry(string $country, bool $transition = false): array
    {
        return self::list(null, $country, $transition);
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

