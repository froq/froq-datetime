<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime;

/**
 * @package froq\datetime
 * @class   froq\datetime\EpochException
 * @author  Kerem Güneş
 * @since   6.0
 */
class EpochException extends DateTimeException
{
    public static function forInvalidDateTime(mixed $when): static
    {
        return ($when === null)
             ? new static('Invalid date/time: null')
             : new static('Invalid date/time: %q', $when);
    }
}
