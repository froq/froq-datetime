<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * A read-only locale info entry class.
 *
 * @package froq\date
 * @object  froq\date\LocaleInfo
 * @author  Kerem Güneş
 * @since   6.0
 * @internal
 */
final class LocaleInfo
{
    /**
     * Constructor.
     */
    public function __construct(
        public readonly string  $language,
        public readonly ?string $country  = null,
        public readonly ?string $encoding = null,
    )
    {}
}
