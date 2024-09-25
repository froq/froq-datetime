<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime;

use froq\common\interface\{Arrayable, Lengthable};

/**
 * An extended `DatePeriod` class.
 *
 * @package froq\datetime
 * @class   froq\datetime\Period
 * @author  Kerem Güneş
 * @since   6.0
 */
class Period extends \DatePeriod implements Arrayable, Lengthable
{
    /**
     * Constructor.
     *
     * Note: This method swaps the parameters order and merges `$recurrences`
     * parameter with `$end` as it's already overloaded internally. It also
     * includes end date as default.
     *
     * @override
     */
    function __construct(
        \DateTimeInterface|string $start, \DateTimeInterface|int $end = null, \DateInterval $interval = null,
        bool $includeStartDate = true, bool $includeEndDate = true, bool $convert = false
    ) {
        // Handle ISO strings.
        if (is_string($start)) {
            $period = self::createFromString($start, $includeStartDate, $includeEndDate);

            [$start, $end, $interval] = [$period->getStartDate(), $period->getEndDate(), $period->interval];
        }
        // Normalize integer ends.
        elseif (is_int($end)) {
            $period = new \DatePeriod($start, $interval, $end);
            if ($ending = self::getLastDate($period)) {
                $end = $ending;
            }
        }

        // Convert internal dates.
        if ($convert) {
            $start = self::convert($start);
            $end && $end = self::convert($end);
        }

        parent::__construct(
            $start, $interval, $end,
            self::options($includeStartDate, $includeEndDate)
        );
    }

    /**
     * Check if start date included.
     *
     * @return bool
     */
    public function startDateIncluded(): bool
    {
        return $this->include_start_date;
    }

    /**
     * Check if end date included.
     *
     * @return bool
     */
    public function endDateIncluded(): bool
    {
        return $this->include_end_date;
    }

    /**
     * Note: Available if start date less than end date.
     *
     * @override
     */
    public function getCurrentDate(bool $convert = false): \DateTimeInterface|null
    {
        return $convert ? self::convert($this->current) : $this->current;
    }

    /**
     * @override
     */
    public function getStartDate(bool $convert = false): \DateTimeInterface
    {
        return $convert ? self::convert($this->start) : $this->start;
    }

    /**
     * @override
     */
    public function getEndDate(bool $convert = false): \DateTimeInterface|null
    {
        if ($this->end) {
            return $convert ? self::convert($this->end) : $this->end;
        }

        // In case if end is null (@see constructor).
        if ($this->recurrences && ($end = iter_last($this))) {
            return $convert ? self::convert($end) : $end;
        }

        return null;
    }

    /**
     * @override
     */
    public function getRecurrences(): int
    {
        return parent::getRecurrences() ?? $this->recurrences;
    }

    /**
     * @return froq\datetime\Interval
     * @override
     */
    public function getDateInterval(): Interval
    {
        return new Interval(parent::getDateInterval());
    }

    /**
     * @alias getDateInterval()
     */
    public function getInterval()
    {
        return $this->getDateInterval();
    }

    /**
     * @inheritDoc froq\common\interface\Arrayable
     */
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    /**
     * @inheritDoc froq\common\interface\Lengthable
     */
    public function length(): int
    {
        return iterator_count($this);
    }

    /**
     * Create from (ISO) string.
     *
     * @param  string $specification
     * @param  bool   $includeStartDate
     * @param  bool   $includeEndDate
     * @param  bool   $convert
     * @return froq\datetime\Period
     */
    public static function createFromString(
        string $specification, bool $includeStartDate = true, bool $includeEndDate = true,
        bool $convert = false
    ) : Period
    {
        $options = self::options($includeStartDate, $includeEndDate);

        if (PHP_VERSION_ID < 80300) {
            $period = new \DatePeriod($specification, $options);
        } else {
            $period = \DatePeriod::createFromISO8601String($specification, $options);
        }

        return new Period(
            self::getFirstDate($period), self::getLastDate($period),
            $period->interval, $includeStartDate, $includeEndDate, $convert
        );
    }

    /**
     * Get first date of given period if available.
     *
     * @param  DatePeriod $period
     * @return DateTimeInterface|null
     */
    public static function getFirstDate(\DatePeriod $period, bool $convert = false): \DateTimeInterface|null
    {
        $datetime = iter_first($period);

        return $convert ? self::convert($datetime) : $datetime;
    }

    /**
     * Get last date of given period if available.
     *
     * @param  DatePeriod $period
     * @return DateTimeInterface|null
     */
    public static function getLastDate(\DatePeriod $period, bool $convert = false): \DateTimeInterface|null
    {
        $datetime = iter_last($period);

        return $convert ? self::convert($datetime) : $datetime;
    }

    /**
     * @internal
     */
    private static function convert(\DateTimeInterface|null $datetime): \DateTimeInterface|null
    {
        if ($datetime && !$datetime instanceof DateTime) {
            $datetime = new DateTime($datetime);
        }

        return $datetime;
    }

    /**
     * Prepare options.
     *
     * @internal
     */
    private static function options(bool $includeStartDate, bool $includeEndDate): int
    {
        return (
            (!$includeStartDate ? parent::EXCLUDE_START_DATE : 0) |
            ($includeEndDate ? parent::INCLUDE_END_DATE : 0)
        );
    }
}
