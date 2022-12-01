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
    /**
     * Create for invalid id.
     *
     * @param  string $id
     * @return static
     */
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

    /**
     * Create for last error.
     *
     * @return static
     */
    public static function forLastError(): static
    {
        return new static(new \LastError());
    }
}
