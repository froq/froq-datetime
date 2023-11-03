<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime\format;

/**
 * @package froq\datetime\format
 * @class   froq\datetime\format\FormatException
 * @author  Kerem Güneş
 * @since   6.0
 */
class FormatException extends \froq\datetime\DateTimeException
{
    public static function forEmptyFormat(): static
    {
        return new static('No format yet, call setFormat() or pass $format argument');
    }

    public static function forInvalidFormat(string $format): static
    {
        return new static('Invalid format: %q', $format);
    }
}
