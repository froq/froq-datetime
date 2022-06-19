<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-date
 */
declare(strict_types=1);

namespace froq\date;

use DateTime;

/**
 * Unix time utility class.
 *
 * @package froq\date
 * @object  froq\date\UnixTime
 * @author  Kerem Güneş
 * @since   4.0, 5.0, 6.0
 */
class UnixTime
{
    /** @var int */
    protected int $time;

    /**
     * Constructor.
     *
     * @param  int|string|froq\date\Date|DateTime|null $when
     * @throws froq\date\UnixTimeException
     */
    public function __construct(int|string|Date|DateTime $when = null)
    {
        if ($when !== null) {
            if (is_int($when)) {
                $this->time = $when;
            } else {
                $this->time = self::convert($when) ??
                    throw new UnixTimeException('Invalid time: ' . $when);
            }
        }
    }

    /**
     * Set time.
     *
     * @param  int $time
     * @return void
     */
    public function setTime(int $time): void
    {
        $this->time = $time;
    }

    /**
     * Get time.
     *
     * @return int|null
     */
    public function getTime(): int|null
    {
        return $this->time ?? null;
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

    /**
     * Make a Unix time.
     *
     * @param  int      $hour,
     * @param  int|null $minute
     * @param  int|null $second
     * @param  int|null $month
     * @param  int|null $day
     * @param  int|null $year
     * @return int|null
     */
    public static function make(
        int $year = null, int $month = null, int $day = null,
        int $hour = null, int $minute = null, int $second = null,
    ): int|null
    {
        $defs = array_map('intval', explode('-', date('Y-m-d-H-i-s')));

        $time =@ mktime(
            $hour ?? $defs[3], $minute ?? $defs[4], $second ?? $defs[5],
            $month ?? $defs[1], $day ?? $defs[2], $year ?? $defs[0]
        );
        return ($time !== false) ? $time : null;
    }

    /**
     * Convert given date to Unix time.
     *
     * @param  string|froq\date\Date|DateTime $when
     * @return int|null
     */
    public static function convert(string|Date|DateTime $when): int|null
    {
        if (is_string($when)) {
            $time =@ strtotime($when);
            return ($time !== false) ? $time : null;
        }
        return $when->getTimestamp();
    }
}

