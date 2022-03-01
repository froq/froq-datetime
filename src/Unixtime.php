<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * Unixtime.
 *
 * Just for only syntactic sugar delight.
 *
 * @package froq\date
 * @object  froq\date\Unixtime
 * @author  Kerem Güneş
 * @since   4.0, 5.0
 * @static
 */
final class Unixtime
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
     * @param  string $when
     * @return int
     * @since  5.2
     */
    public static function from(string $when): int
    {
        return strtotime($when);
    }
}

