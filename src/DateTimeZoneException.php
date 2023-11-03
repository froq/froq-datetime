<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime;

/**
 * @package froq\datetime
 * @class   froq\datetime\DateTimeZoneException
 * @author  Kerem Güneş
 * @since   4.5, 6.0
 */
class DateTimeZoneException extends DateTimeException
{
    public static function forEmptyId(): static
    {
        return new static('Empty time zone id');
    }
}
