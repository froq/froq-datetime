<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * A class for (default) locale only.
 *
 * @package froq\date
 * @object  froq\date\Locale
 * @author  Kerem Güneş
 * @since   6.0
 */
class Locale
{
    /** @const int */
    public final const DEFAULT = 'en_US.UTF-8';

    /**
     * Get LC_TIME value or default.
     *
     * @param  string $default
     * @return string
     */
    public static final function default(string $default = self::DEFAULT): string
    {
        return getlocale(LC_TIME, default: $default);
    }
}
