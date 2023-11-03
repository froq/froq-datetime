<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime;

use froq\common\interface\Arrayable;

/**
 * An extended `DateInterval` class.
 *
 * @package froq\datetime
 * @class   froq\datetime\Interval
 * @author  Kerem Güneş
 * @since   6.0
 */
class Interval extends \DateInterval implements Arrayable
{
    /**
     * Constructor.
     *
     * @param  string|DateInterval|null $duration
     * @causes Exception
     * @override
     */
    public function __construct(string|\DateInterval $duration = null)
    {
        if ($duration instanceof \DateInterval) {
            $this->copy($duration, $this);
        } else {
            // Prevent format error with 0 values.
            $duration ??= 'P0Y';

            parent::__construct($duration);
        }
    }

    /**
     * Get year.
     *
     * @return int
     */
    public function getYear(): int
    {
        return $this->y;
    }

    /**
     * Get month.
     *
     * @return int
     */
    public function getMonth(): int
    {
        return $this->m;
    }

    /**
     * Get day.
     *
     * @return int
     */
    public function getDay(): int
    {
        return $this->d;
    }

    /**
     * Get hour.
     *
     * @return int
     */
    public function getHour(): int
    {
        return $this->h;
    }

    /**
     * Get minute.
     *
     * @return int
     */
    public function getMinute(): int
    {
        return $this->i;
    }

    /**
     * Get second.
     *
     * @return int
     */
    public function getSecond(): int
    {
        return $this->s;
    }

    /**
     * Get microsecond.
     *
     * @return int
     */
    public function getMicrosecond(): int
    {
        return (int) ($this->f * 1000000);
    }

    /**
     * Get fraction.
     *
     * @return float
     */
    public function getFraction(): float
    {
        return $this->f;
    }

    /**
     * Get days.
     *
     * @return int|false
     */
    public function getDays(): int|false
    {
        return $this->days;
    }

    /**
     * Parameterized static initializer.
     *
     * Note: Sometimes property "days" cannot be set manually, so
     * unaffected by the parameter $days, dunno..
     *
     * @param  int|null       $year
     * @param  int|null       $month
     * @param  int|null       $day
     * @param  int|null       $hour
     * @param  int|null       $minute
     * @param  int|null       $second
     * @param  int|float|null $fraction
     * @param  int|null       $days
     * @return froq\datetime\Interval
     */
    public static function of(
        int $year = null, int $month = null, int $day = null,
        int $hour = null, int $minute = null, int $second = null,
        int|float $fraction = null, int $days = null
    ): Interval
    {
        $that = new Interval(sprintf(
            'P%dY%dM%dDT%dH%dM%dS',
            $year, $month, $day,
            $hour, $minute, $second
        ));

        // Must set manually.
        $that->f = (float) $fraction;
        if ($days !== null) {
            $that->days = (int) $days;
        }

        return $that;
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return [
            'year' => $this->y, 'month' => $this->m, 'day' => $this->d,
            'hour' => $this->h, 'minute' => $this->i, 'second' => $this->s,
            'fraction' => $this->f, 'days' => $this->days
        ];
    }

    /**
     * Copy source interval properties to target interval.
     */
    private function copy(\DateInterval $source, \DateInterval $target): void
    {
        foreach (get_object_vars($source) as $name => $value) {
            $target->$name = $value;
        }
    }
}
