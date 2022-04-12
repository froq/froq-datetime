<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * A extended UTC date class, derived from `Date` class.
 *
 * @package froq\date
 * @object  froq\date\UtcDate
 * @author  Kerem Güneş
 * @since   4.0
 */
class UtcDate extends Date
{
    /**
     * Constructor.
     *
     * @param string|int|float|null $when
     */
    public function __construct(string|int|float $when = null)
    {
        parent::__construct($when, 'UTC');
    }
}

