<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

namespace froq\Date;

/**
 * Unixtime.
 *
 * Just for only syntactic sugar delight.
 *
 * @package froq\date
 * @object  froq\date\Unixtime
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0, 5.0 Renamed from Timestamp.
 * @static
 */
final class Unixtime
{
    /**
     * Now.
     * @return int
     */
    public static function now(): int
    {
        return time();
    }
}

