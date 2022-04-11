<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * A format pattern class to use in `Formatter` class.
 *
 * @package froq\date
 * @object  froq\date\Format
 * @author  Kerem Güneş
 * @since   6.0
 */
class Format
{
    /**
     * Constructor.
     *
     * @param string $pattern
     */
    public function __construct(
        public string $pattern = ''
    ) {}

    /**
     * Set pattern.
     *
     * @param  string $pattern
     * @return void
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * Get pattern.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }
}
