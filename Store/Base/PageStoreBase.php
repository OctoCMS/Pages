<?php

/**
 * Page base store for table: page

 */

namespace Octo\Pages\Store\Base;

use Block8\Database\Connection;
use Octo\Store;
use Octo\Pages\Model\Page;
use Octo\Pages\Model\PageCollection;
use Octo\Pages\Store\PageStore;

/**
 * Page Base Store
 */
class PageStoreBase extends Store
{
    /** @var PageStore $instance */
    protected static $instance = null;

    /** @var string */
    protected $table = 'page';

    /** @var string */
    protected $model = 'Octo\Pages\Model\Page';

    /** @var string */
    protected $key = 'id';

    /**
     * Return the database store for this model.
     * @return PageStore
     */
    public static function load() : PageStore
    {
        if (is_null(self::$instance)) {
            self::$instance = new PageStore(Connection::get());
        }

        return self::$instance;
    }

    /**
    * @param $value
    * @return Page|null
    */
    public function getByPrimaryKey($value)
    {
        return $this->getById($value);
    }


    /**
     * Get a Page object by Id.
     * @param $value
     * @return Page|null
     */
    public function getById(string $value)
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
     * Get all Page objects by ParentId.
     * @return \Octo\Pages\Model\PageCollection
     */
    public function getByParentId($value, $limit = null)
    {
        return $this->where('parent_id', $value)->get($limit);
    }

    /**
     * Gets the total number of Page by ParentId value.
     * @return int
     */
    public function getTotalByParentId($value) : int
    {
        return $this->where('parent_id', $value)->count();
    }

    /**
     * Get all Page objects by CurrentVersionId.
     * @return \Octo\Pages\Model\PageCollection
     */
    public function getByCurrentVersionId($value, $limit = null)
    {
        return $this->where('current_version_id', $value)->get($limit);
    }

    /**
     * Gets the total number of Page by CurrentVersionId value.
     * @return int
     */
    public function getTotalByCurrentVersionId($value) : int
    {
        return $this->where('current_version_id', $value)->count();
    }

    /**
     * Get all Page objects by ContentTypeId.
     * @return \Octo\Pages\Model\PageCollection
     */
    public function getByContentTypeId($value, $limit = null)
    {
        return $this->where('content_type_id', $value)->get($limit);
    }

    /**
     * Gets the total number of Page by ContentTypeId value.
     * @return int
     */
    public function getTotalByContentTypeId($value) : int
    {
        return $this->where('content_type_id', $value)->count();
    }

    /**
     * Get a Page object by Uri.
     * @param $value
     * @return Page|null
     */
    public function getByUri(string $value)
    {
        return $this->where('uri', $value)->first();
    }
}
