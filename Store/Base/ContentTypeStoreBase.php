<?php

/**
 * ContentType base store for table: content_type
 */

namespace Octo\Pages\Store\Base;

use PDOException;
use b8\Cache;
use b8\Database;
use b8\Database\Query;
use b8\Database\Query\Criteria;
use b8\Exception\StoreException;
use Octo\Store;
use Octo\Pages\Model\ContentType;
use Octo\Pages\Model\ContentTypeCollection;

/**
 * ContentType Base Store
 */
trait ContentTypeStoreBase
{
    protected function init()
    {
        $this->tableName = 'content_type';
        $this->modelName = '\Octo\Pages\Model\ContentType';
        $this->primaryKey = 'id';
    }
    /**
    * @param $value
    * @param string $useConnection Connection type to use.
    * @throws StoreException
    * @return ContentType
    */
    public function getByPrimaryKey($value, $useConnection = 'read')
    {
        return $this->getById($value, $useConnection);
    }


    /**
    * @param $value
    * @param string $useConnection Connection type to use.
    * @throws StoreException
    * @return ContentType
    */
    public function getById($value, $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new StoreException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }
        // This is the primary key, so try and get from cache:
        $cacheResult = $this->getFromCache($value);

        if (!empty($cacheResult)) {
            return $cacheResult;
        }


        $query = new Query($this->getNamespace('ContentType').'\Model\ContentType', $useConnection);
        $query->select('*')->from('content_type')->limit(1);
        $query->where('`id` = :id');
        $query->bind(':id', $value);

        try {
            $query->execute();
            $result = $query->fetch();

            $this->setCache($value, $result);

            return $result;
        } catch (PDOException $ex) {
            throw new StoreException('Could not get ContentType by Id', 0, $ex);
        }
    }

    /**
     * @param $value
     * @param array $options Offsets, limits, etc.
     * @param string $useConnection Connection type to use.
     * @throws StoreException
     * @return int
     */
    public function getTotalForParentId($value, $options = [], $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new StoreException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = new Query($this->getNamespace('ContentType').'\Model\ContentType', $useConnection);
        $query->from('content_type')->where('`parent_id` = :parent_id');
        $query->bind(':parent_id', $value);

        $this->handleQueryOptions($query, $options);

        try {
            return $query->getCount();
        } catch (PDOException $ex) {
            throw new StoreException('Could not get count of ContentType by ParentId', 0, $ex);
        }
    }

    /**
     * @param $value
     * @param array $options Limits, offsets, etc.
     * @param string $useConnection Connection type to use.
     * @throws StoreException
     * @return ContentTypeCollection
     */
    public function getByParentId($value, $options = [], $useConnection = 'read')
    {
        if (is_null($value)) {
            throw new StoreException('Value passed to ' . __FUNCTION__ . ' cannot be null.');
        }

        $query = new Query($this->getNamespace('ContentType').'\Model\ContentType', $useConnection);
        $query->from('content_type')->where('`parent_id` = :parent_id');
        $query->bind(':parent_id', $value);

        $this->handleQueryOptions($query, $options);

        try {
            $query->execute();
            return new ContentTypeCollection($query->fetchAll());
        } catch (PDOException $ex) {
            throw new StoreException('Could not get ContentType by ParentId', 0, $ex);
        }

    }
}
