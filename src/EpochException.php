<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
declare(strict_types=1);

namespace froq\datetime;

/**
 * @package froq\datetime
 * @object  froq\datetime\EpochException
 * @author  Kerem Güneş
 * @since   6.0
 */
class EpochException extends DateTimeException
{
    /**
     * Create for invalid date/time.
     *
     * @param  mixed $when
     * @return static
     */
    public static function forInvalidDateTime(mixed $when): static
    {
        return match (true) {
            ($when === null) => new static('Invalid date/time: null'),
            ($when === '') => new static('Invalid date/time: \'\''),
            default => new static('Invalid date/time: %q', $when),
        };
    }
}
