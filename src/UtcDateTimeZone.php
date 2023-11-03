<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime;

/**
 * A extended `DateTimeZone` class with UTC time zone.
 *
 * @package froq\datetime
 * @class   froq\datetime\UtcDateTimeZone
 * @author  Kerem Güneş
 * @since   6.0
 */
class UtcDateTimeZone extends DateTimeZone
{
    /**
     * Constructor.
     *
     * @override
     */
    public function __construct()
    {
        parent::__construct('UTC');
    }
}

