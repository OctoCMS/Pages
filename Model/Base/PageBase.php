<?php

/**
 * Page base model for table: page
 */

namespace Octo\Pages\Model\Base;

use b8\Store\Factory;

/**
 * Page Base Model
 */
trait PageBase
{
    protected function init()
    {
        $this->tableName = 'page';
        $this->modelName = 'Page';

        // Columns:
        $this->data['id'] = null;
        $this->getters['id'] = 'getId';
        $this->setters['id'] = 'setId';
        $this->data['parent_id'] = null;
        $this->getters['parent_id'] = 'getParentId';
        $this->setters['parent_id'] = 'setParentId';
        $this->data['current_version_id'] = null;
        $this->getters['current_version_id'] = 'getCurrentVersionId';
        $this->setters['current_version_id'] = 'setCurrentVersionId';
        $this->data['uri'] = null;
        $this->getters['uri'] = 'getUri';
        $this->setters['uri'] = 'setUri';
        $this->data['position'] = null;
        $this->getters['position'] = 'getPosition';
        $this->setters['position'] = 'setPosition';
        $this->data['content_type_id'] = null;
        $this->getters['content_type_id'] = 'getContentTypeId';
        $this->setters['content_type_id'] = 'setContentTypeId';
        $this->data['publish_date'] = null;
        $this->getters['publish_date'] = 'getPublishDate';
        $this->setters['publish_date'] = 'setPublishDate';
        $this->data['expiry_date'] = null;
        $this->getters['expiry_date'] = 'getExpiryDate';
        $this->setters['expiry_date'] = 'setExpiryDate';

        // Foreign keys:
        $this->getters['CurrentVersion'] = 'getCurrentVersion';
        $this->setters['CurrentVersion'] = 'setCurrentVersion';
        $this->getters['Parent'] = 'getParent';
        $this->setters['Parent'] = 'setParent';
        $this->getters['ContentType'] = 'getContentType';
        $this->setters['ContentType'] = 'setContentType';
    }
    /**
    * Get the value of Id / id.
    *
    * @return string
    */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
    * Get the value of ParentId / parent_id.
    *
    * @return string
    */
    public function getParentId()
    {
        $rtn = $this->data['parent_id'];

        return $rtn;
    }

    /**
    * Get the value of CurrentVersionId / current_version_id.
    *
    * @return int
    */
    public function getCurrentVersionId()
    {
        $rtn = $this->data['current_version_id'];

        return $rtn;
    }

    /**
    * Get the value of Uri / uri.
    *
    * @return string
    */
    public function getUri()
    {
        $rtn = $this->data['uri'];

        return $rtn;
    }

    /**
    * Get the value of Position / position.
    *
    * @return int
    */
    public function getPosition()
    {
        $rtn = $this->data['position'];

        return $rtn;
    }

    /**
    * Get the value of ContentTypeId / content_type_id.
    *
    * @return int
    */
    public function getContentTypeId()
    {
        $rtn = $this->data['content_type_id'];

        return $rtn;
    }

    /**
    * Get the value of PublishDate / publish_date.
    *
    * @return \DateTime
    */
    public function getPublishDate()
    {
        $rtn = $this->data['publish_date'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
    }

    /**
    * Get the value of ExpiryDate / expiry_date.
    *
    * @return \DateTime
    */
    public function getExpiryDate()
    {
        $rtn = $this->data['expiry_date'];

        if (!empty($rtn)) {
            $rtn = new \DateTime($rtn);
        }

        return $rtn;
    }


    /**
    * Set the value of Id / id.
    *
    * Must not be null.
    * @param $value string
    */
    public function setId($value)
    {
        $this->validateString('Id', $value);
        $this->validateNotNull('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;
        $this->setModified('id');
    }

    /**
    * Set the value of ParentId / parent_id.
    *
    * @param $value string
    */
    public function setParentId($value)
    {
        $this->validateString('ParentId', $value);

        // As this is a foreign key, empty values should be treated as null:
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['parent_id'] === $value) {
            return;
        }

        $this->data['parent_id'] = $value;
        $this->setModified('parent_id');
    }

    /**
    * Set the value of CurrentVersionId / current_version_id.
    *
    * @param $value int
    */
    public function setCurrentVersionId($value)
    {
        $this->validateInt('CurrentVersionId', $value);

        // As this is a foreign key, empty values should be treated as null:
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['current_version_id'] === $value) {
            return;
        }

        $this->data['current_version_id'] = $value;
        $this->setModified('current_version_id');
    }

    /**
    * Set the value of Uri / uri.
    *
    * Must not be null.
    * @param $value string
    */
    public function setUri($value)
    {
        $this->validateString('Uri', $value);
        $this->validateNotNull('Uri', $value);

        if ($this->data['uri'] === $value) {
            return;
        }

        $this->data['uri'] = $value;
        $this->setModified('uri');
    }

    /**
    * Set the value of Position / position.
    *
    * Must not be null.
    * @param $value int
    */
    public function setPosition($value)
    {
        $this->validateInt('Position', $value);
        $this->validateNotNull('Position', $value);

        if ($this->data['position'] === $value) {
            return;
        }

        $this->data['position'] = $value;
        $this->setModified('position');
    }

    /**
    * Set the value of ContentTypeId / content_type_id.
    *
    * Must not be null.
    * @param $value int
    */
    public function setContentTypeId($value)
    {
        $this->validateInt('ContentTypeId', $value);

        // As this is a foreign key, empty values should be treated as null:
        if (empty($value)) {
            $value = null;
        }

        $this->validateNotNull('ContentTypeId', $value);

        if ($this->data['content_type_id'] === $value) {
            return;
        }

        $this->data['content_type_id'] = $value;
        $this->setModified('content_type_id');
    }

    /**
    * Set the value of PublishDate / publish_date.
    *
    * @param $value \DateTime
    */
    public function setPublishDate($value)
    {
        $this->validateDate('PublishDate', $value);

        if ($this->data['publish_date'] === $value) {
            return;
        }

        $this->data['publish_date'] = $value;
        $this->setModified('publish_date');
    }

    /**
    * Set the value of ExpiryDate / expiry_date.
    *
    * @param $value \DateTime
    */
    public function setExpiryDate($value)
    {
        $this->validateDate('ExpiryDate', $value);

        if ($this->data['expiry_date'] === $value) {
            return;
        }

        $this->data['expiry_date'] = $value;
        $this->setModified('expiry_date');
    }
    /**
    * Get the PageVersion model for this Page by Id.
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

        return Factory::getStore('PageVersion', 'Octo\Pages')->getById($key);
    }

    /**
    * Set CurrentVersion - Accepts an ID, an array representing a PageVersion or a PageVersion model.
    *
    * @param $value mixed
    */
    public function setCurrentVersion($value)
    {
        // Is this an instance of PageVersion?
        if ($value instanceof \Octo\Pages\Model\PageVersion) {
            return $this->setCurrentVersionObject($value);
        }

        // Is this an array representing a PageVersion item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setCurrentVersionId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setCurrentVersionId($value);
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
    * Get the Page model for this Page by Id.
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

        return Factory::getStore('Page', 'Octo\Pages')->getById($key);
    }

    /**
    * Set Parent - Accepts an ID, an array representing a Page or a Page model.
    *
    * @param $value mixed
    */
    public function setParent($value)
    {
        // Is this an instance of Page?
        if ($value instanceof \Octo\Pages\Model\Page) {
            return $this->setParentObject($value);
        }

        // Is this an array representing a Page item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setParentId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setParentId($value);
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
    * Get the ContentType model for this Page by Id.
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

        return Factory::getStore('ContentType', 'Octo\Pages')->getById($key);
    }

    /**
    * Set ContentType - Accepts an ID, an array representing a ContentType or a ContentType model.
    *
    * @param $value mixed
    */
    public function setContentType($value)
    {
        // Is this an instance of ContentType?
        if ($value instanceof \Octo\Pages\Model\ContentType) {
            return $this->setContentTypeObject($value);
        }

        // Is this an array representing a ContentType item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setContentTypeId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setContentTypeId($value);
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
}
