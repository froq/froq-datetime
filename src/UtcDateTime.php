<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime;

/**
 * A extended `DateTime` class with UTC time zone.
 *
 * @package froq\datetime
 * @class   froq\datetime\UtcDateTime
 * @author  Kerem Güneş
 * @since   4.0, 6.0
 */
class UtcDateTime extends DateTime
{
    /**
     * Constructor.
     *
     * @param int|float|string|\DateTimeInterface|null $when
     * @override
     */
    public function __construct(int|float|string|\DateTimeInterface $when = null)
    {
        parent::__construct($when, new UtcDateTimeZone());
    }
}

