<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime;

use froq\datetime\zone\{Zone, ZoneId, ZoneUtil};
use froq\common\interface\Stringable;

/**
 * An extended `DateTimeZone` class.
 *
 * @package froq\datetime
 * @class   froq\datetime\DateTimeZone
 * @author  Kerem Güneş
 * @since   4.0, 6.0
 */
class DateTimeZone extends \DateTimeZone implements Stringable, \Stringable, \JsonSerializable
{
    /**
     * Time zone types.
     * https://github.com/php/php-src/blob/master/ext/date/lib/timelib.h#L328
     */
    public const TYPE_NONE   = 0, // Unknown.
                 TYPE_OFFSET = 1, // Eg: +00 or +00:00.
                 TYPE_ABBR   = 2, // Eg: GMT or Z.
                 TYPE_ID     = 3; // Eg: UTC or Etc/UTC.

    /**
     * Constructor.
     *
     * @param  string $id
     * @throws froq\datetime\DateTimeZoneException
     */
    public function __construct(string $id)
    {
        // Default id (eg: default or @default).
        if (preg_test('~^[@]?default$~i', $id)) {
            $id = Zone::default();
        }

        try {
            parent::__construct($id);
        } catch (\Throwable $e) {
            throw ($id === '') // Empty id.
                ? DateTimeZoneException::forEmptyId($e)
                : DateTimeZoneException::forCaughtThrowable($e);
        }
    }

    /**
     * @magic
     * @missing
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType(): int
    {
        return ((array) $this)['timezone_type'];
    }

    /**
     * Get ID (valid for timezone_type=3).
     *
     * @return string
     */
    public function getId(): string
    {
        if ($this->getType() === self::TYPE_ID) {
            return parent::getName();
        }

        return '';
    }

    /**
     * Get abbr.
     *
     * @return string
     */
    public function getAbbr(): string
    {
        // @cancel: Not implemented yet internally.
        // if (strtoupper($this->getId()) === 'EUROPE/ISTANBUL') {
        //     return 'TRT';
        // }

        return (new \DateTime('', $this))->format('T');
    }

    /**
     * Get location (valid for timezone_type=3).
     *
     * @return array
     * @override
     */
    public function getLocation(): array
    {
        if ($this->getType() === self::TYPE_ID) {
            $ret = parent::getLocation();

            // Normalize.
            foreach ($ret as $key => $value) {
                if (!$value || $value === '??' || $value === '?') {
                    $ret[$key] = null;
                }
            }

            return $ret;
        }

        return [];
    }

    /**
     * Get a suitable name for presentation.
     *
     * @return string
     * @missing
     */
    public function getDisplayName(): string
    {
        return ZoneUtil::idToName($this->getId());
    }

    /**
     * Get offset.
     *
     * @param  DateTimeInterface|null $datetime
     * @return int
     * @override
     */
    public function getOffset(\DateTimeInterface $datetime = null): int
    {
        $datetime ??= new \DateTime('', $this);

        return $datetime->getOffset();
    }

    /**
     * Get offset code.
     *
     * @param  DateTimeInterface|null $datetime
     * @return string
     * @missing
     */
    public function getOffsetCode(\DateTimeInterface $datetime = null): string
    {
        $datetime ??= new \DateTime('', $this);

        return $datetime->format('P');
    }

    /**
     * Get a detailed zone info.
     *
     * @return froq\datetime\zone\Zone
     */
    public function toZone(): Zone
    {
        return new Zone($this->getId());
    }

    /**
     * Get a detailed zone id info.
     *
     * @return froq\datetime\zone\ZoneId
     */
    public function toZoneId(): ZoneId
    {
        return new ZoneId($this->getId());
    }

    /**
     * @inheritDoc froq\common\interface\Stringable
     */
    public function toString(): string
    {
        return $this->getName();
    }

    /**
     * @inheritDoc JsonSerializable
     */
    public function jsonSerialize(): string
    {
        return (string) $this;
    }

    /**
     * Bridge method to froq\datetime\zone\Zone::normalizeId() method.
     */
    public static function normalizeId(string $id): string
    {
        return Zone::normalizeId($id);
    }

    /**
     * Bridge method to froq\datetime\zone\Zone::validateId() method.
     */
    public static function validateId(string $id): bool
    {
        return Zone::validateId($id);
    }

    /**
     * Bridge method to froq\datetime\zone\Zone::default() method.
     */
    public static function default(string $id = null): string
    {
        return Zone::default($id);
    }

    /**
     * Bridge method to froq\datetime\zone\Zone::defaultOffset() method.
     */
    public static function defaultOffset(): int
    {
        return Zone::defaultOffset();
    }
}
