<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
declare(strict_types=1);

namespace froq\datetime;

/**
 * Epoch (Unix time) class.
 *
 * @package froq\datetime
 * @object  froq\datetime\Epoch
 * @author  Kerem Güneş
 * @since   4.0, 5.0, 6.0
 */
class Epoch
{
    /** Timestamp. */
    private int $time;

    /**
     * Constructor.
     *
     * @param  int|float|string|DateTimeInterface|null $when Default is time().
     * @throws froq\datetime\EpochException
     */
    public function __construct(int|float|string|\DateTimeInterface $when = null)
    {
        if (func_num_args()) {
            if ($when === null || $when === '') {
                throw EpochException::forInvalidDateTime($when);
            }

            $this->time = self::convert($when) ??
                throw EpochException::forInvalidDateTime($when);
        } else {
            $this->time = self::now();
        }
    }

    /**
     * Set time.
     *
     * @param  int $time
     * @return self
     */
    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time.
     *
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * Format.
     *
     * @param  string $format
     * @return string
     */
    public function format(string $format): string
    {
        return date($format, $this->time);
    }

    /**
     * Format UTC.
     *
     * @param  string $format
     * @return string
     */
    public function formatUtc(string $format): string
    {
        return gmdate($format, $this->time);
    }

    /**
     * Parameterized static initializer.
     *
     * @param  int      $hour
     * @param  int|null $minute
     * @param  int|null $second
     * @param  int|null $month
     * @param  int|null $day
     * @param  int|null $year
     * @return int|null
     */
    public static function of(
        int $year = null, int $month = null, int $day = null,
        int $hour = null, int $minute = null, int $second = null,
    ): int|null
    {
        // Defaults.
        $defs = array_map('intval', explode('-', date('Y-m-d-H-i-s')));

        $time =@ mktime(
            $hour ?? $defs[3], $minute ?? $defs[4], $second ?? $defs[5],
            $month ?? $defs[1], $day ?? $defs[2], $year ?? $defs[0]
        );

        return ($time !== false) ? $time : null;
    }

    /**
     * Parameterized static initializer for UTC.
     *
     * @param  int      $hour
     * @param  int|null $minute
     * @param  int|null $second
     * @param  int|null $month
     * @param  int|null $day
     * @param  int|null $year
     * @return int|null
     */
    public static function ofUtc(
        int $year = null, int $month = null, int $day = null,
        int $hour = null, int $minute = null, int $second = null,
    ): int|null
    {
        // Defaults.
        $defs = array_map('intval', explode('-', gmdate('Y-m-d-H-i-s')));

        $time =@ gmmktime(
            $hour ?? $defs[3], $minute ?? $defs[4], $second ?? $defs[5],
            $month ?? $defs[1], $day ?? $defs[2], $year ?? $defs[0]
        );

        return ($time !== false) ? $time : null;
    }

    /**
     * Convert given input to epoch (Unix time).
     *
     * @param  int|float|string|DateTimeInterface $when
     * @return int|null
     */
    public static function convert(int|float|string|\DateTimeInterface $when): int|null
    {
        if ($when instanceof \DateTimeInterface) {
            return $when->getTimestamp();
        }

        if (is_number($when)) {
            return (int) $when;
        }

        $time =@ strtotime($when);
        return ($time !== false) ? $time : null;
    }

    /**
     * Now.
     *
     * @return int
     */
    public static function now(): int
    {
        return time();
    }
}

