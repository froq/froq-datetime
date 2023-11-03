<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime\format;

/**
 * A format pattern class.
 *
 * @package froq\datetime\format
 * @class   froq\datetime\format\Format
 * @author  Kerem Güneş
 * @since   6.0
 */
class Format implements \Stringable
{
    /** Patterns. */
    public const
        // Locale formats.
        LOCALE        = '%d %B %Y, %R',
        LOCALE_SHORT  = '%d %B %Y',

        // Locale formats for ago's.
        AGO           = '%d %B %Y, %R',
        AGO_SHORT     = '%d %B %Y',

        // HTTP formats.
        HTTP          = 'D, d M Y H:i:s \G\M\T', // @rfc7231
        HTTP_COOKIE   = 'D, d M Y H:i:s \G\M\T', // @rfc6265

        // ISO formats.
        ISO           = 'Y-m-d\TH:i:sp',
        ISO_MS        = 'Y-m-d\TH:i:s.up',

        // SQL formats.
        SQL           = 'Y-m-d H:i:s',
        SQL_MS        = 'Y-m-d H:i:s.u'
    ;

    /** Format pattern. */
    private string $pattern = '';

    /**
     * Constructor.
     *
     * @param string|null $pattern
     */
    public function __construct(string $pattern = null)
    {
        if ($pattern !== null) {
            $this->pattern = trim($pattern);
        }
    }

    /**
     * @magic
     */
    public function __toString(): string
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
