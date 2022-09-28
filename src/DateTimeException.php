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
    /** Error details. */
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
