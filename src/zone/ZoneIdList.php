<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-datetime
 */
declare(strict_types=1);

namespace froq\datetime\zone;

/**
 * A list class for listing time zone ids.
 *
 * @package froq\datetime\zone
 * @object  froq\datetime\zone\ZoneIdList
 * @author  Kerem Güneş
 * @since   6.0
 */
class ZoneIdList extends \ItemList
{
    /**
     * Constructor.
     *
     * @param string|int|null $group
     * @param string|null     $country
     * @override
     */
    public function __construct(string|int $group = null, string $country = null)
    {
        $items = ZoneUtil::listIds($group, $country);
        $items->map(fn(string $id): ZoneId => new ZoneId($id));

        parent::__construct($items);
    }

    /**
     * @override
     */
    public function toArray(bool $deep = false): array
    {
        $items = parent::toArray();

        if ($deep) foreach ($items as &$item) {
            if ($item instanceof ZoneId) {
                $item = $item->toArray();
            }
        }

        return $items;
    }
}
