<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

/**
 * A factory class for building Date instances.
 *
 * @package froq\date
 * @object  froq\date\Dater
 * @author  Kerem Güneş
 * @since   6.0
 */
class Dater
{
    /**
     * Constructor.
     *
     * @param int|null    $year
     * @param int|null    $month
     * @param int|null    $day
     * @param int|null    $hour
     * @param int|null    $minute
     * @param int|null    $second
     * @param int|null    $microsecond
     * @param string|null $timezone
     * @param string|null $locale
     */
    public function __construct(
        private ?int $year = null, private ?int $month = null, private ?int $day = null,
        private ?int $hour = null, private ?int $minute = null, private ?int $second = null,
        private ?int $microsecond = null, private ?string $timezone = null, private ?string $locale = null
    )
    {}

    /**
     * Set year.
     *
     * @param  int $year
     * @return self
     */
    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    /**
     * Get year.
     *
     * @return int|null
     */
    public function getYear(): int|null
    {
        return $this->year;
    }

    /**
     * Set month.
     *
     * @param  int $month
     * @return self
     */
    public function setMonth(int $month): self
    {
        $this->month = $month;
        return $this;
    }

    /**
     * Get month.
     *
     * @return int|null
     */
    public function getMonth(): int|null
    {
        return $this->month;
    }

    /**
     * Set day.
     *
     * @param  int $day
     * @return self
     */
    public function setDay(int $day): self
    {
        $this->day = $day;
        return $this;
    }

    /**
     * Get day.
     *
     * @return int|null
     */
    public function getDay(): int|null
    {
        return $this->day;
    }

    /**
     * Set hour.
     *
     * @param  int $hour
     * @return self
     */
    public function setHour(int $hour): self
    {
        $this->hour = $hour;
        return $this;
    }

    /**
     * Get hour.
     *
     * @return int|null
     */
    public function getHour(): int|null
    {
        return $this->hour;
    }

    /**
     * Set minute.
     *
     * @param  int $minute
     * @return self
     */
    public function setMinute(int $minute): self
    {
        $this->minute = $minute;
        return $this;
    }

    /**
     * Get minute.
     *
     * @return int|null
     */
    public function getMinute(): int|null
    {
        return $this->minute;
    }

    /**
     * Set second.
     *
     * @param  int $second
     * @return self
     */
    public function setSecond(int $second): self
    {
        $this->second = $second;
        return $this;
    }

    /**
     * Get second.
     *
     * @return int|null
     */
    public function getSecond(): int|null
    {
        return $this->second;
    }

    /**
     * Set microsecond.
     *
     * @param  int $microsecond
     * @return self
     */
    public function setMicrosecond(int $microsecond): self
    {
        $this->microsecond = $microsecond;
        return $this;
    }

    /**
     * Get microsecond.
     *
     * @return int|null
     */
    public function getMicrosecond(): int|null
    {
        return $this->microsecond;
    }

    /**
     * Set timezone.
     *
     * @param  string $timezone
     * @return self
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * Get timezone.
     *
     * @return string|null
     */
    public function getTimezone(): string|null
    {
        return $this->timezone;
    }

    /**
     * Set locale.
     *
     * @param  string $locale
     * @return self
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get locale.
     *
     * @return string|null
     */
    public function getLocale(): string|null
    {
        return $this->locale;
    }

    /**
     * Get date.
     *
     * @return string
     */
    public function getDate(): string
    {
        return $this->format('Y-m-d');
    }

    /**
     * Get time.
     *
     * @return string
     */
    public function getTime(): string
    {
        return $this->format('H:i:s.u');
    }

    /**
     * Convert to Date instance.
     *
     * @return froq\date\Date
     */
    public function toDate(): Date
    {
        return Date::make(
            $this->year, $this->month, $this->day,
            $this->hour, $this->minute, $this->second,
            $this->microsecond, $this->timezone, $this->locale
        );
    }

    /**
     * Convert to UtcDate instance.
     *
     * @return froq\date\UtcDate
     */
    public function toUtcDate(): UtcDate
    {
        return UtcDate::make(
            $this->year, $this->month, $this->day,
            $this->hour, $this->minute, $this->second,
            $this->microsecond, 'UTC', $this->locale
        );
    }

    /**
     * Format converting to Date instance.
     *
     * @param  string $format
     * @return string
     */
    public function format(string $format): string
    {
        return $this->toDate()->format($format);
    }

    /**
     * Format converting to UtcDate instance.
     *
     * @param  string $format
     * @return string
     */
    public function formatUtc(string $format): string
    {
        return $this->toUtcDate()->format($format);
    }

    /**
     * @alias setHour()
     */
    public function setHours(int $hours): self
    {
        return $this->setHour($hours);
    }

    /**
     * @alias getHour()
     */
    public function getHours(): int|null
    {
        return $this->getHour();
    }

    /**
     * @alias setMinute()
     */
    public function setMinutes(int $minutes): self
    {
        return $this->setMinute($minutes);
    }

    /**
     * @alias getMinute()
     */
    public function getMinutes(): int|null
    {
        return $this->getMinute();
    }

    /**
     * @alias setSecond()
     */
    public function setSeconds(int $seconds): self
    {
        return $this->setSecond($seconds);
    }

    /**
     * @alias getSecond()
     */
    public function getSeconds(): int|null
    {
        return $this->getSecond();
    }

    /**
     * @alias setMicrosecond()
     */
    public function setMicroseconds(int $microseconds): self
    {
        return $this->setMicrosecond($microseconds);
    }

    /**
     * @alias getMicrosecond()
     */
    public function getMicroseconds(): int|null
    {
        return $this->getMicrosecond();
    }
}
