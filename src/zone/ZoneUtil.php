<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
declare(strict_types=1);

namespace froq\datetime\zone;

/**
 * Zone utility class.
 *
 * @package froq\datetime\zone
 * @object  froq\datetime\zone\ZoneUtil
 * @author  Kerem Güneş
 * @since   6.0
 * @static
 */
class ZoneUtil extends \StaticClass
{
    /**
     * List ids.
     *
     * @param  string|int|null $group
     * @param  string|null     country
     * @return Set
     * @throws froq\datetime\zone\ZoneException
     */
    public static function listIds(string|int $group = null, string $country = null): \Set
    {
        $group ??= \DateTimeZone::ALL;

        if (is_string($group)) {
            $given = $group;
            $group = strtoupper($group);

            // As shortcut.
            if ($group === 'COUNTRY') {
                $group = 'PER_COUNTRY';
            }

            $constant = 'DateTimeZone::' . $group;

            if (!defined($constant)) {
                throw new ZoneException(
                    'Invalid group %q, use a valid DateTimeZone constant name',
                    $given
                );
            }

            $group = constant($constant);
        }

        if ($group === \DateTimeZone::PER_COUNTRY) {
            $given = $country;

            // For typos (eg: tr => TR).
            $country && $country = strtoupper($country);

            // Act like original.
            if (!$country || strlen($country) != 2) {
                throw new ZoneException(
                    'Argument $country must be a two-letter ISO 3166-1 compatible country '.
                    'code when argument $group is DateTimeZone::PER_COUNTRY, %s given',
                    $given
                );
            }
        }

        $items = array_flip(\DateTimeZone::listIdentifiers($group, $country));

        // Move UTC to top.
        if (isset($items['UTC'])) {
            $items = ['UTC' => $items['UTC']] + $items;
        }

        return new \Set(array_keys($items));
    }

    /**
     * Convert an id to name.
     *
     * @param  string $id
     * @return string
     */
    public static function idToName(string $id): string
    {
        return str_replace(['/', '_'], [' / ', ' '], $id);
    }

    /**
     * Convert an offset to code.
     *
     * @param  int $offset
     * @return string
     */
    public static function offsetToCode(int $offset): string
    {
        // https://github.com/php/php-src/blob/master/ext/date/php_date.c#L1944
        return sprintf(
            '%s%02d:%02d',
            $offset < 0 ? '-' : '+',
            abs((int) ($offset / 3600)),
            abs((int) ($offset % 3600) / 60)
        );
    }
}
