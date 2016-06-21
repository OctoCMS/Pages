<?php

/**
 * Page model collection
 */

namespace Octo\Pages\Model;

use Block8\Database\Model\Collection;

/**
 * Page Model Collection
 */
class PageCollection extends Collection
{
    /**
     * Add a Page model to the collection.
     * @param string $key
     * @param Page $value
     * @return PageCollection
     */
    public function addPage($key, Page $value)
    {
        return parent::add($key, $value);
    }

    /**
     * @param $key
     * @return Page|null
     */
    public function get($key)
    {
        return parent::get($key);
    }
}
