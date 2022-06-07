<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * A read-only diff entry class.
 *
 * @package froq\date
 * @object  froq\date\Diff
 * @author  Kerem Güneş
 * @since   5.2
 * @internal
 */
final class Diff
{
    /**
     * Constructor.
     */
    public function __construct(
        public readonly int $year        = 0,
        public readonly int $month       = 0,
        public readonly int $day         = 0,
        public readonly int $days        = 0,
        public readonly int $hour        = 0,
        public readonly int $minute      = 0,
        public readonly int $second      = 0,
        public readonly int $microsecond = 0,
    )
    {}
}
