<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime\zone;

use froq\datetime\DateTimeZone;
use froq\common\interface\Arrayable;

/**
 * Time zone id class with some details & utility methods.
 *
 * @package froq\datetime\zone
 * @class   froq\datetime\zone\ZoneId
 * @author  Kerem Güneş
 * @since   6.0
 */
class ZoneId implements Arrayable, \Stringable
{
    /** Zone id. */
    public readonly string $id;

    /** Zone name. */
    public readonly string $name;

    /**
     * Constructor.
     *
     * @param string      $id
     * @param string|null $name
     */
    public function __construct(string $id, string $name = null)
    {
        $this->id   = $id;
        $this->name = $name ?? ZoneUtil::idToName($id);
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
     * Get named.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get as Zone instance.
     *
     * @return froq\datetime\zone\Zone
     */
    public function toZone(): Zone
    {
        return new Zone($this->id);
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
        return ['id' => $this->id, 'name' => $this->name];
    }
}
