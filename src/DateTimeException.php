<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
declare(strict_types=1);

namespace froq\datetime;

/**
 * @package froq\datetime
 * @object  froq\datetime\DateTimeException
 * @author  Kerem Güneş
 * @since   4.0, 6.0
 */
class DateTimeException extends \froq\common\Exception
{
    /**
     * Error details.
     *
     * @var array|null
     */
    private ?array $errors = null;

    /**
     * Constructor.
     *
     * @param ...$arguments Same as froq\common\Exception.
     * @override
     */
    public function __construct(...$arguments)
    {
        if ($errors = array_pluck($arguments, 'errors')) {
            $this->errors = array_values($errors);
        }

        parent::__construct(...$arguments);
    }

    /**
     * Get errors property.
     *
     * @return array|null
     */
    public function errors(): array|null
    {
        return $this->errors;
    }

    /**
     * Create for failed modification.
     *
     * @param  array|null $errors
     * @return static
     */
    public static function forFailedModification(array|null $errors): static
    {
        return new static(
            error_message(extract: true) ?: 'Modification failed',
            errors: $errors
        );
    }

    /**
     * Create for invalid date.
     *
     * @param  string $date
     * @return static
     */
    public static function forInvalidDate(string $date): static
    {
        return new static(
            'Invalid date: %q (use a parsable date, eg: 2022-01-01)',
            $date
        );
    }

    /**
     * Create for invalid time.
     *
     * @param  string $time
     * @return static
     */
    public static function forInvalidTime(string $time): static
    {
        return new static(
            'Invalid time: %q (use a parsable time, eg: 22:11:19 or 22:11:19.123345)',
            $time
        );
    }

    /**
     * Create for caught throwable.
     *
     * @param  Throwable $e
     * @return static
     */
    public static function forCaughtThrowable(\Throwable $e): static
    {
        return new static($e, extract: true);
    }
}
