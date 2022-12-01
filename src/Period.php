<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
namespace froq\datetime;

use froq\common\interface\Arrayable;

/**
 * An extended `DatePeriod` class.
 *
 * @package froq\datetime
 * @class   froq\datetime\Period
 * @author  Kerem Güneş
 * @since   6.0
 */
class Period extends \DatePeriod implements Arrayable
{
    /**
     * Get interval.
     *
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
}
