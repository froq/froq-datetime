<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

use DateTimeZone;

/**
 * Time zone list class for listing available time zones.
 *
 * @package froq\date
 * @object  froq\date\TimeZoneList
 * @author  Kerem Güneş
 * @since   6.0
 */
class TimeZoneList extends \ItemList
{
    /**
     * Constructor.
     *
     * @param string|int|null $group
     * @param string|null     $country
     * @param bool            $transition
     */
    public function __construct(string|int $group = null, string $country = null, bool $transition = false)
    {
        parent::__construct(self::list($group, $country, $transition));
    }

    /**
     * @override
     */
    public function toArray(bool $deep = false): array
    {
        $items = parent::toArray();

        if ($deep) foreach ($items as &$item) {
            if ($item instanceof TimeZoneInfo) {
                $item = (array) $item;
            }
        }

        return $items;
    }

    /**
     * List.
     *
     * @param  string|int|null $group
     * @param  string|null     $country
     * @param  bool            $transition
     * @return froq\date\TimeZoneInfo[]
     * @throws froq\date\TimeZoneException
     */
    public static function list(string|int $group = null, string $country = null, bool $transition = false): array
    {
        if ($group == null && $country != null) {
            $group = DateTimeZone::PER_COUNTRY;
        }

        try {
            if ($group != null) {
                // For typos (eg: tr => TR).
                $country && $country = strtoupper($country);

                // Name of constant.
                if (is_string($group)) {
                    $constant = 'DateTimeZone::' . strtoupper($group);
                    defined($constant) || throw new TimeZoneException(
                        'Invalid group %s, use a valid DateTimeZone constant name', $group
                    );

                    $ids = DateTimeZone::listIdentifiers(constant($constant), $country);
                } else {
                    $ids = DateTimeZone::listIdentifiers($group, $country);
                }
            } else {
                $ids = DateTimeZone::listIdentifiers();
            }
        } catch (TimeZoneException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new TimeZoneException($e);
        }

        $ret = [];

        // Always first.
        if ($group == null) {
            $ret[] = TimeZone::makeInfo('UTC', $transition);
        }

        foreach ($ids as $id) {
            // Already set first.
            if ($group == null && $id == 'UTC') {
                continue;
            }

            $ret[] = TimeZone::makeInfo($id, $transition);
        }

        return $ret;
    }

    /**
     * List by given group.
     *
     * @param  string|int $group
     * @param  bool       $transition
     * @return froq\date\TimeZoneInfo[]
     */
    public static function listGroup(string|int $group, bool $transition = false): array
    {
        return self::list($group, null, $transition);
    }

    /**
     * List by given country.
     *
     * @param  string $country
     * @param  bool   $transition
     * @return froq\date\TimeZoneInfo[]
     */
    public static function listCountry(string $country, bool $transition = false): array
    {
        return self::list(null, $country, $transition);
    }
}
