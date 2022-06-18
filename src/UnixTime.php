<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

use DateTime;

/**
 * Just for only syntactic sugar delight.
 *
 * @package froq\date
 * @object  froq\date\UnixTime
 * @author  Kerem Güneş
 * @since   4.0, 5.0
 * @static
 */
class UnixTime
{
    /**
     * Now.
     *
     * @return int
     */
    public static function now(): int
    {
        return time();
    }

    /**
     * Convert given date to unixtime.
     *
     * @param  string|Date|DateTime $when
     * @return int
     */
    public static function from(string|Date|DateTime $when): int
    {
        return is_string($when) ? strtotime($when) : $when->getTimestamp();
    }
}

