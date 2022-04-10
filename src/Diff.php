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
 * A read-only diff entry class.
 *
 * @package froq\date
 * @object  froq\date\Diff
 * @author  Kerem Güneş
 * @since   5.2
 * @internal
 */
final class Diff implements Arrayable, Objectable
{
    /**
     * Constructor.
     */
    public function __construct(
        private array $dates,
        private int $year,
        private int $month,
        private int $day,
        private int $days,
        private int $hour,
        private int $minute,
        private int $second,
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
            'dates'       => $this->dates, 'year' => $this->year, 'month'  => $this->month,  'day'    => $this->day,
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
