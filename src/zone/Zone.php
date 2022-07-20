<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
declare(strict_types=1);

namespace froq\datetime\zone;

use froq\datetime\DateTimeZone;
use froq\common\interface\Arrayable;

/**
 * Time zone class with some details & utility methods.
 *
 * @package froq\datetime\zone
 * @object  froq\datetime\zone\Zone
 * @author  Kerem Güneş
 * @since   4.0, 6.0
 */
class Zone extends Zones implements Arrayable, \Stringable
{
    /** Default id as fallback. */
    public const DEFAULT = 'UTC';

    /** Zone id. */
    public readonly string $id;

    /** Zone name. */
    public readonly string $name;

    /** Zone offset. */
    public readonly int $offset;

    /** Zone offset code */
    public readonly string $offsetCode;

    /**
     * Constructor.
     *
     * @param string  $id
     * @param bool ...$options
     */
    public function __construct(string $id, bool ...$options)
    {
        if (!empty($options['normalize'])) {
            $id = self::normalizeId($id);
        }
        if (!empty($options['validate']) && !self::validateId($id)) {
            throw ZoneException::forInvalidId($id);
        }

        try {
            $now = new \DateTime('', new \DateTimeZone($id));
        } catch (\Throwable $e) {
            throw ZoneException::forCaughtThrowable($e);
        }

        $this->id         = $id;
        $this->name       = ZoneUtil::idToName($id);
        $this->offset     = $now->getOffset();
        $this->offsetCode = $now->format('P');
    }

    /**
     * @magic
     */
    public function __toString()
    {
        return $this->id;
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get offset.
     *
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Get offset code.
     *
     * @return string
     */
    public function getOffsetCode(): string
    {
        return $this->offsetCode;
    }

    /**
     * Get as DateTimeZone instance.
     *
     * @return froq\datetime\DateTimeZone
     */
    public function toDateTimeZone(): DateTimeZone
    {
        return new DateTimeZone($this->id);
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name,
                'offset' => $this->offset, 'offsetCode' => $this->offsetCode];
    }

    /**
     * List available zones.
     *
     * @param  string|int|null  $group
     * @param  string|null      $country
     * @return froq\datetime\zone\ZoneList
     */
    public static function list(string|int $group = null, string $country = null): ZoneList
    {
        return new ZoneList($group, $country);
    }

    /**
     * List available zone ids.
     *
     * @param  string|int|null  $group
     * @param  string|null      $country
     * @return froq\datetime\zone\ZoneIdList
     */
    public static function listIds(string|int $group = null, string $country = null): ZoneIdList
    {
        return new ZoneIdList($group, $country);
    }

    /**
     * Normalize an id (eg: EUROPE/ISTANBUL => Europe/Istanbul).
     *
     * @param  string $id
     * @return string
     */
    public static function normalizeId(string $id): string
    {
        $id = strtoupper($id);
        if ($id == 'UTC' || $id == 'GMT' || str_contains($id, ':')) {
            return $id;
        }

        // Previous char-map to look up.
        static $map = ['/' => 1, '_' => 1, '-' => 1];

        $ret = '';

        for ($i = 0, $il = strlen($id); $i < $il; $i++) {
            $chr = $id[$i];
            if ($i > 0 && !isset($map[$id[$i - 1]])) {
                $chr = strtolower($chr);
            }
            $ret .= $chr;
        }

        return $ret;
    }

    /**
     * Validate an id.
     *
     * @param  string $id
     * @return bool
     */
    public static function validateId(string $id): bool
    {
        // Eg: "UTC" or "Europe/Istanbul", "Z" or "+03:00" is not valid.
        if (!$id || !preg_test('~^(UTC|\w+/[\w\-\/]+)$~i', $id)) {
            return false;
        }

        try {
            new \DateTimeZone($id);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Set/get default time zone.
     *
     * @param  string|null $id
     * @return string
     * @throws froq\datetime\zone\ZoneException
     */
    public static function default(string $id = null): string
    {
        if ($id !== null) {
            if (!self::validateId($id)) {
                throw ZoneException::forInvalidId($id);
            }
            if (!@date_default_timezone_set($id)) {
                throw ZoneException::forLastError();
            }
        }

        return date_default_timezone_get() ?: static::DEFAULT;
    }

    /**
     * Get default time zone offset.
     *
     * @return int
     */
    public static function defaultOffset(): int
    {
        return (new DateTimeZone(self::default()))->getOffset();
    }
}
