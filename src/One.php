<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * A class for one (moment) constants only.
 *
 * @package froq\date
 * @object  froq\date\One
 * @author  Kerem Güneş
 * @since   6.0
 */
class One
{
    /** @const int */
    public final const MINUTE = 60,
                       HOUR   = 3600,
                       DAY    = 86400,
                       WEEK   = 604800, // 86400 * 7
                       MONTH  = 2592000, // 86400 * 30
                       YEAR   = 31536000; // 86400 * 365

}
