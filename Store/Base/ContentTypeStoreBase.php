<?php

/**
 * ContentType base store for table: content_type

 */

namespace Octo\Pages\Store\Base;

use Octo\Store;
use Octo\Pages\Model\ContentType;
use Octo\Pages\Model\ContentTypeCollection;

/**
 * ContentType Base Store
 */
class ContentTypeStoreBase extends Store
{
    protected $table = 'content_type';
    protected $model = 'Octo\Pages\Model\ContentType';
    protected $key = 'id';

    /**
    * @param $value
    * @return ContentType|null
    */
    public function getByPrimaryKey($value)
    {
        return $this->getById($value);
    }


    /**
     * Get a ContentType object by Id.
     * @param $value
     * @return ContentType|null
     */
    public function getById(int $value)
    {
        // This is the primary key, so try and get from cache:
        $cacheResult = $this->cacheGet($value);

        if (!empty($cacheResult)) {
            return $cacheResult;
        }

        $rtn = $this->where('id', $value)->first();
        $this->cacheSet($value, $rtn);

        return $rtn;
    }

    /**
     * Get all ContentType objects by ParentId.
     * @return \Octo\Pages\Model\ContentTypeCollection
     */
    public function getByParentId($value, $limit = null)
    {
        return $this->where('parent_id', $value)->get($limit);
    }

    /**
     * Gets the total number of ContentType by ParentId value.
     * @return int
     */
    public function getTotalByParentId($value) : int
    {
        return $this->where('parent_id', $value)->count();
    }
}
