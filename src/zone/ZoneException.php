<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime\zone;

/**
 * @package froq\datetime\zone
 * @class   froq\datetime\zone\ZoneException
 * @author  Kerem Güneş
 * @since   4.5, 6.0
 */
class ZoneException extends \froq\datetime\DateTimeException
{
    public static function forInvalidId(string $id): static
    {
        if ($id === '') {
            return new static('Empty time zone id');
        } else {
            return new static(
                'Invalid time zone id: %q (use UTC or Xxx/Xxx format)',
                $id
            );
        }
    }

    public static function forInvalidGroup(string $group): static
    {
        return new static(
            'Invalid group %q, use a valid DateTimeZone constant name',
            $group
        );
    }

    public static function forInvalidCountry(string $country): static
    {
        return new static(
            'Argument $country must be a two-letter ISO 3166-1 compatible country '.
            'code when argument $group is DateTimeZone::PER_COUNTRY, %s given',
            $country
        );
    }

    public static function forLastError(): static
    {
        return new static(new \LastError());
    }
}
