<?php

/**
 * ContentType model collection
 */

namespace Octo\Pages\Model;

use Block8\Database\Model\Collection;

/**
 * ContentType Model Collection
 */
class ContentTypeCollection extends Collection
{
    /**
     * Add a ContentType model to the collection.
     * @param string $key
     * @param ContentType $value
     * @return ContentTypeCollection
     */
    public function addContentType($key, ContentType $value)
    {
        return parent::add($key, $value);
    }

    /**
     * @param $key
     * @return ContentType|null
     */
    public function get($key)
    {
        return parent::get($key);
    }
}
