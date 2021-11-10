<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

use froq\common\interface\{Arrayable, Objectable};

/**
 * Diff.
 *
 * Represents a read-only diff entity that intented to use internally.
 *
 * @package froq\date
 * @object  froq\date\DateException
 * @author  Kerem Güneş
 * @since   5.2
 * @internal
 */
class Diff implements Arrayable, Objectable
{
    /**
     * Constructor.
     */
    public function __construct(
        /** @var array */
        private array $dates,

        /** @var int */
        private int $year,

        /** @var int */
        private int $month,

        /** @var int */
        private int $day,

        /** @var int */
        private int $days,

        /** @var int */
        private int $hour,

        /** @var int */
        private int $minute,

        /** @var int */
        private int $second,

        /** @var int */
        private int $millisecond,
    )
    {}

    /**
     * Get dates.
     *
     * @return array
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    /**
     * Get year.
     *
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Get month.
     *
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * Get day.
     *
     * @return int
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * Get days.
     *
     * @return int
     */
    public function getDays(): int
    {
        return $this->days;
    }

    /**
     * Get hour.
     *
     * @return int
     */
    public function getHour(): int
    {
        return $this->hour;
    }

    /**
     * Get minute.
     *
     * @return int
     */
    public function getMinute(): int
    {
        return $this->minute;
    }

    /**
     * Get second.
     *
     * @return int
     */
    public function getSecond(): int
    {
        return $this->second;
    }

    /**
     * Get millisecond.
     *
     * @return int
     */
    public function getMillisecond(): int
    {
        return $this->millisecond;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return [
            'dates'       => $this->dates, 'year' => $this->year, 'month'  => $this->month, 'day'    => $this->day,
            'days'        => $this->days,  'hour' => $this->hour, 'minute' => $this->minute, 'second' => $this->second,
            'millisecond' => $this->millisecond
        ];
    }

    /**
     * @inheritDoc froq\common\interface\Objectable
     */
    public function toObject(): object
    {
        return (object) $this->toArray();
    }
}
