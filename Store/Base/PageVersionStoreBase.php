<?php

/**
 * PageVersion base store for table: page_version

 */

namespace Octo\Pages\Store\Base;

use Block8\Database\Connection;
use Octo\Store;
use Octo\Pages\Model\PageVersion;
use Octo\Pages\Model\PageVersionCollection;
use Octo\Pages\Store\PageVersionStore;

/**
 * PageVersion Base Store
 */
class PageVersionStoreBase extends Store
{
    /** @var PageVersionStore $instance */
    protected static $instance = null;

    /** @var string */
    protected $table = 'page_version';

    /** @var string */
    protected $model = 'Octo\Pages\Model\PageVersion';

    /** @var string */
    protected $key = 'id';

    /**
     * Return the database store for this model.
     * @return PageVersionStore
     */
    public static function load() : PageVersionStore
    {
        if (is_null(self::$instance)) {
            self::$instance = new PageVersionStore(Connection::get());
        }

        return self::$instance;
    }

    /**
    * @param $value
    * @return PageVersion|null
    */
    public function getByPrimaryKey($value)
    {
        return $this->getById($value);
    }


    /**
     * Get a PageVersion object by Id.
     * @param $value
     * @return PageVersion|null
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
     * Get all PageVersion objects by PageId.
     * @return \Octo\Pages\Model\PageVersionCollection
     */
    public function getByPageId($value, $limit = null)
    {
        return $this->where('page_id', $value)->get($limit);
    }

    /**
     * Gets the total number of PageVersion by PageId value.
     * @return int
     */
    public function getTotalByPageId($value) : int
    {
        return $this->where('page_id', $value)->count();
    }

    /**
     * Get all PageVersion objects by ContentItemId.
     * @return \Octo\Pages\Model\PageVersionCollection
     */
    public function getByContentItemId($value, $limit = null)
    {
        return $this->where('content_item_id', $value)->get($limit);
    }

    /**
     * Gets the total number of PageVersion by ContentItemId value.
     * @return int
     */
    public function getTotalByContentItemId($value) : int
    {
        return $this->where('content_item_id', $value)->count();
    }

    /**
     * Get all PageVersion objects by UserId.
     * @return \Octo\Pages\Model\PageVersionCollection
     */
    public function getByUserId($value, $limit = null)
    {
        return $this->where('user_id', $value)->get($limit);
    }

    /**
     * Gets the total number of PageVersion by UserId value.
     * @return int
     */
    public function getTotalByUserId($value) : int
    {
        return $this->where('user_id', $value)->count();
    }

    /**
     * Get all PageVersion objects by ImageId.
     * @return \Octo\Pages\Model\PageVersionCollection
     */
    public function getByImageId($value, $limit = null)
    {
        return $this->where('image_id', $value)->get($limit);
    }

    /**
     * Gets the total number of PageVersion by ImageId value.
     * @return int
     */
    public function getTotalByImageId($value) : int
    {
        return $this->where('image_id', $value)->count();
    }
}
