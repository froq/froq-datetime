<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

namespace froq\Date;

use froq\date\Date;

/**
 * UTC Date.
 *
 * @package froq\date
 * @object  froq\date\UtcDate
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 */
class UtcDate extends Date
{
    /**
     * Constructor.
     * @param string|int|float|null $when
     */
    public function __construct(string|int|float $when = null)
    {
        parent::__construct($when, 'UTC');
    }
}

