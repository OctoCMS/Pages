<?php

/**
 * PageVersion model collection
 */

namespace Octo\Pages\Model;

use Octo;
use b8\Model\Collection;

/**
 * PageVersion Model Collection
 */
class PageVersionCollection extends Collection
{
    /**
     * Add a PageVersion model to the collection.
     * @param string $key
     * @param PageVersion $value
     * @return PageVersionCollection
     */
    public function add($key, PageVersion $value)
    {
        return parent::add($key, $value);
    }

    /**
     * @param $key
     * @return PageVersion|null
     */
    public function get($key)
    {
        return parent::get($key);
    }
}
