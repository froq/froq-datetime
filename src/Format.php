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
    /** @const string */
    public final const LOCALE        = '%d %B %Y, %R',
                       LOCALE_SHORT  = '%d %B %Y',
                       AGO           = '%d %B %Y, %R',
                       AGO_SHORT     = '%d %B %Y',
                       HTTP          = 'D, d M Y H:i:s \G\M\T', // @rfc7231
                       HTTP_COOKIE   = self::HTTP,              // @rfc6265
                       ISO           = 'Y-m-d\TH:i:sP',
                       ISO_MS        = 'Y-m-d\TH:i:s.uP',
                       ISO_UTC       = 'Y-m-d\TH:i:s\Z',
                       ISO_UTC_MS    = 'Y-m-d\TH:i:s.u\Z',
                       SQL           = 'Y-m-d H:i:s',
                       SQL_MS        = 'Y-m-d H:i:s.u';

    /**
     * Constructor.
     *
     * @param string $pattern
     */
    public function __construct(
        private string $pattern = ''
    ) {}

    /**
     * @magic
     */
    public function __toString()
    {
        return $this->pattern;
    }

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
