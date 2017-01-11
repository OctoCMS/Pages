<?php

/**
 * Page base model for table: page
 */

namespace Octo\Pages\Model\Base;

use DateTime;
use Block8\Database\Query;
use Octo\Model;
use Octo\Store;
use Octo\Pages\Model\Page;
use Octo\Pages\Store\PageStore;

/**
 * Page Base Model
 */
abstract class PageBase extends Model
{
    protected $table = 'page';
    protected $model = 'Page';
    protected $data = [
        'id' => null,
        'parent_id' => null,
        'current_version_id' => null,
        'content_type_id' => 1,
        'uri' => '',
        'position' => 0,
        'publish_date' => null,
        'expiry_date' => null,
    ];

    protected $getters = [
        'id' => 'getId',
        'parent_id' => 'getParentId',
        'current_version_id' => 'getCurrentVersionId',
        'content_type_id' => 'getContentTypeId',
        'uri' => 'getUri',
        'position' => 'getPosition',
        'publish_date' => 'getPublishDate',
        'expiry_date' => 'getExpiryDate',
        'CurrentVersion' => 'getCurrentVersion',
        'Parent' => 'getParent',
        'ContentType' => 'getContentType',
    ];

    protected $setters = [
        'id' => 'setId',
        'parent_id' => 'setParentId',
        'current_version_id' => 'setCurrentVersionId',
        'content_type_id' => 'setContentTypeId',
        'uri' => 'setUri',
        'position' => 'setPosition',
        'publish_date' => 'setPublishDate',
        'expiry_date' => 'setExpiryDate',
        'CurrentVersion' => 'setCurrentVersion',
        'Parent' => 'setParent',
        'ContentType' => 'setContentType',
    ];

    /**
     * Return the database store for this model.
     * @return PageStore
     */
    public static function Store() : PageStore
    {
        return PageStore::load();
    }

    /**
     * Get Page by primary key: id
     * @param string $id
     * @return Page|null
     */
    public static function get(string $id) : ?Page
    {
        return self::Store()->getById($id);
    }

    /**
     * @throws \Exception
     * @return Page
     */
    public function save() : Page
    {
        $rtn = self::Store()->save($this);

        if (empty($rtn)) {
            throw new \Exception('Failed to save Page');
        }

        if (!($rtn instanceof Page)) {
            throw new \Exception('Unexpected ' . get_class($rtn) . ' received from save.');
        }

        $this->data = $rtn->toArray();

        return $this;
    }


    /**
     * Get the value of Id / id
     * @return string
     */

     public function getId() : string
     {
        $rtn = $this->data['id'];

        return $rtn;
     }
    
    /**
     * Get the value of ParentId / parent_id
     * @return string
     */

     public function getParentId() : ?string
     {
        $rtn = $this->data['parent_id'];

        return $rtn;
     }
    
    /**
     * Get the value of CurrentVersionId / current_version_id
     * @return int
     */

     public function getCurrentVersionId() : ?int
     {
        $rtn = $this->data['current_version_id'];

        return $rtn;
     }
    
    /**
     * Get the value of ContentTypeId / content_type_id
     * @return int
     */

     public function getContentTypeId() : int
     {
        $rtn = $this->data['content_type_id'];

        return $rtn;
     }
    
    /**
     * Get the value of Uri / uri
     * @return string
     */

     public function getUri() : string
     {
        $rtn = $this->data['uri'];

        return $rtn;
     }
    
    /**
     * Get the value of Position / position
     * @return int
     */

     public function getPosition() : int
     {
        $rtn = $this->data['position'];

        return $rtn;
     }
    
    /**
     * Get the value of PublishDate / publish_date
     * @return DateTime
     */

     public function getPublishDate() : ?DateTime
     {
        $rtn = $this->data['publish_date'];

        if (!empty($rtn)) {
            $rtn = new DateTime($rtn);
        }

        return $rtn;
     }
    
    /**
     * Get the value of ExpiryDate / expiry_date
     * @return DateTime
     */

     public function getExpiryDate() : ?DateTime
     {
        $rtn = $this->data['expiry_date'];

        if (!empty($rtn)) {
            $rtn = new DateTime($rtn);
        }

        return $rtn;
     }
    
    
    /**
     * Set the value of Id / id
     * @param $value string
     * @return Page
     */
    public function setId(string $value) : Page
    {

        if ($this->data['id'] !== $value) {
            $this->data['id'] = $value;
            $this->setModified('id');
        }

        return $this;
    }
    
    /**
     * Set the value of ParentId / parent_id
     * @param $value string
     * @return Page
     */
    public function setParentId(?string $value) : Page
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['parent_id'] !== $value) {
            $this->data['parent_id'] = $value;
            $this->setModified('parent_id');
        }

        return $this;
    }
    
    /**
     * Set the value of CurrentVersionId / current_version_id
     * @param $value int
     * @return Page
     */
    public function setCurrentVersionId(?int $value) : Page
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['current_version_id'] !== $value) {
            $this->data['current_version_id'] = $value;
            $this->setModified('current_version_id');
        }

        return $this;
    }
    
    /**
     * Set the value of ContentTypeId / content_type_id
     * @param $value int
     * @return Page
     */
    public function setContentTypeId(int $value) : Page
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['content_type_id'] !== $value) {
            $this->data['content_type_id'] = $value;
            $this->setModified('content_type_id');
        }

        return $this;
    }
    
    /**
     * Set the value of Uri / uri
     * @param $value string
     * @return Page
     */
    public function setUri(string $value) : Page
    {

        if ($this->data['uri'] !== $value) {
            $this->data['uri'] = $value;
            $this->setModified('uri');
        }

        return $this;
    }
    
    /**
     * Set the value of Position / position
     * @param $value int
     * @return Page
     */
    public function setPosition(int $value) : Page
    {

        if ($this->data['position'] !== $value) {
            $this->data['position'] = $value;
            $this->setModified('position');
        }

        return $this;
    }
    
    /**
     * Set the value of PublishDate / publish_date
     * @param $value DateTime
     * @return Page
     */
    public function setPublishDate($value) : Page
    {
        $this->validateDate('PublishDate', $value);

        if ($this->data['publish_date'] !== $value) {
            $this->data['publish_date'] = $value;
            $this->setModified('publish_date');
        }

        return $this;
    }
    
    /**
     * Set the value of ExpiryDate / expiry_date
     * @param $value DateTime
     * @return Page
     */
    public function setExpiryDate($value) : Page
    {
        $this->validateDate('ExpiryDate', $value);

        if ($this->data['expiry_date'] !== $value) {
            $this->data['expiry_date'] = $value;
            $this->setModified('expiry_date');
        }

        return $this;
    }
    
    
    /**
     * Get the PageVersion model for this  by Id.
     *
     * @uses \Octo\Pages\Store\PageVersionStore::getById()
     * @uses \Octo\Pages\Model\PageVersion
     * @return \Octo\Pages\Model\PageVersion
     */
    public function getCurrentVersion()
    {
        $key = $this->getCurrentVersionId();

        if (empty($key)) {
           return null;
        }

        return Store::get('PageVersion')->getById($key);
    }

    /**
     * Set CurrentVersion - Accepts an ID, an array representing a PageVersion or a PageVersion model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setCurrentVersion($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setCurrentVersionId($value);
        }

        // Is this an instance of CurrentVersion?
        if (is_object($value) && $value instanceof \Octo\Pages\Model\PageVersion) {
            return $this->setCurrentVersionObject($value);
        }

        // Is this an array representing a PageVersion item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setCurrentVersionId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for CurrentVersion.');
    }

    /**
     * Set CurrentVersion - Accepts a PageVersion model.
     *
     * @param $value \Octo\Pages\Model\PageVersion
     */
    public function setCurrentVersionObject(\Octo\Pages\Model\PageVersion $value)
    {
        return $this->setCurrentVersionId($value->getId());
    }

    /**
     * Get the Page model for this  by Id.
     *
     * @uses \Octo\Pages\Store\PageStore::getById()
     * @uses \Octo\Pages\Model\Page
     * @return \Octo\Pages\Model\Page
     */
    public function getParent()
    {
        $key = $this->getParentId();

        if (empty($key)) {
           return null;
        }

        return Store::get('Page')->getById($key);
    }

    /**
     * Set Parent - Accepts an ID, an array representing a Page or a Page model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setParent($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setParentId($value);
        }

        // Is this an instance of Parent?
        if (is_object($value) && $value instanceof \Octo\Pages\Model\Page) {
            return $this->setParentObject($value);
        }

        // Is this an array representing a Page item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setParentId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for Parent.');
    }

    /**
     * Set Parent - Accepts a Page model.
     *
     * @param $value \Octo\Pages\Model\Page
     */
    public function setParentObject(\Octo\Pages\Model\Page $value)
    {
        return $this->setParentId($value->getId());
    }

    /**
     * Get the ContentType model for this  by Id.
     *
     * @uses \Octo\Pages\Store\ContentTypeStore::getById()
     * @uses \Octo\Pages\Model\ContentType
     * @return \Octo\Pages\Model\ContentType
     */
    public function getContentType()
    {
        $key = $this->getContentTypeId();

        if (empty($key)) {
           return null;
        }

        return Store::get('ContentType')->getById($key);
    }

    /**
     * Set ContentType - Accepts an ID, an array representing a ContentType or a ContentType model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setContentType($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setContentTypeId($value);
        }

        // Is this an instance of ContentType?
        if (is_object($value) && $value instanceof \Octo\Pages\Model\ContentType) {
            return $this->setContentTypeObject($value);
        }

        // Is this an array representing a ContentType item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setContentTypeId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for ContentType.');
    }

    /**
     * Set ContentType - Accepts a ContentType model.
     *
     * @param $value \Octo\Pages\Model\ContentType
     */
    public function setContentTypeObject(\Octo\Pages\Model\ContentType $value)
    {
        return $this->setContentTypeId($value->getId());
    }


    public function Pages() : Query
    {
        return Store::get('Page')->where('parent_id', $this->data['id']);
    }

    public function PageVersions() : Query
    {
        return Store::get('PageVersion')->where('page_id', $this->data['id']);
    }
}
