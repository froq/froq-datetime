<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime;

use froq\common\interface\{Arrayable, Stringable};

/**
 * An extended `DateInterval` class.
 *
 * @package froq\datetime
 * @class   froq\datetime\Interval
 * @author  Kerem Güneş
 * @since   6.0
 */
class Interval extends \DateInterval implements Arrayable, Stringable, \Stringable
{
    /**
     * Very interesting that being unable to write
     * `days` property while `f` is writable.
     */
    private int|false|null $_days = null;

    /**
     * Constructor.
     *
     * @param  string|DateInterval|null $duration
     * @causes Exception
     * @override
     */
    public function __construct(string|\DateInterval $duration = null)
    {
        // Store original attributes.
        $fraction = $days = null;

        // Allow "1 Day" like formats.
        if (is_string($duration) && $duration[0] !== 'P') {
            $duration = self::createFormatFromInterval(
                (new \DateTime())->diff(new \DateTime($duration))
            );
        } elseif ($duration instanceof \DateInterval) {
            [$fraction, $days] = [$duration->f, $duration->days];
            $duration = self::createFormatFromInterval($duration);
        }

        // Prevent format error.
        $duration ??= 'PT0S';

        parent::__construct($duration);

        isset($fraction) && $this->f = $fraction;
        isset($days)     && $this->_days = $days;
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
        return $this->_days ?? $this->days;
    }

    /**
     * Check if any diff calculated by date/time fields.
     *
     * @return bool
     */
    public function hasDiff(): bool
    {
        return !!(
            $this->y + $this->m + $this->d +
            $this->h + $this->i + $this->s +
            $this->f
        );
    }

    /**
     * Parameterized static initializer.
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
        $that = Interval::createFromDateString(sprintf(
            '%d year %d month %d day %d hour %d minute %d second %d microsecond',
            $year, $month, $day, $hour, $minute, $second, $fraction * 1000000
        ));

        // Must set manually.
        $that->f = (float) $fraction;
        $that->_days = $days;

        return $that;
    }

    /**
     * @override
     */
    public static function createFromDateString(string $datetime): Interval
    {
        return new Interval(parent::createFromDateString($datetime));
    }

    /**
     * Create a format form given interval.
     *
     * @param  DateInterval $interval
     * @return string
     */
    public static function createFormatFromInterval(\DateInterval $interval): string
    {
        $format = 'P';

        if ($interval->y + $interval->m + $interval->d) {
            $interval->y && $format .= $interval->y . 'Y';
            $interval->m && $format .= $interval->m . 'M';
            $interval->d && $format .= $interval->d . 'D';
        }

        if ($interval->h + $interval->i + $interval->s) {
            // Time separator.
            $format .= 'T';

            $interval->h && $format .= $interval->h . 'H';
            $interval->i && $format .= $interval->i . 'M';
            $interval->s && $format .= $interval->s . 'S';
        }

        return ($format !== 'P') ? $format : 'PT0S';
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return [
            'year' => $this->y, 'month' => $this->m, 'day' => $this->d,
            'hour' => $this->h, 'minute' => $this->i, 'second' => $this->s,
            'fraction' => $this->f, 'days' => $this->getDays()
        ];
    }

    /**
     * @inheritDoc froq\common\interface\Stringable
     */
    public function toString(): string
    {
        return self::createFormatFromInterval($this);
    }

    /**
     * To date/time.
     *
     * @param  string|DateTimeZone|null $where
     * @return froq\datetime\DateTime
     */
    public function toDateTime(string|\DateTimeZone $where = null): DateTime
    {
        return (new DateTime('', $where))->modify($this);
    }
}
