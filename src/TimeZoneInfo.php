<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * A read-only time zone info entry class.
 *
 * @package froq\date
 * @object  froq\date\TimeZoneInfo
 * @author  Kerem Güneş
 * @since   6.0
 * @internal
 */
final class TimeZoneInfo
{
    /**
     * Constructor.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int    $offset,
        public readonly string $offsetCode,
        public readonly ?array $transition = null,
    )
    {}
}
