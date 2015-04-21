<?php

/**
 * ContentType base model for table: content_type
 */

namespace Octo\Pages\Model\Base;

use b8\Store\Factory;

/**
 * ContentType Base Model
 */
trait ContentTypeBase
{
    protected function init()
    {
        $this->tableName = 'content_type';
        $this->modelName = 'ContentType';

        // Columns:
        $this->data['id'] = null;
        $this->getters['id'] = 'getId';
        $this->setters['id'] = 'setId';
        $this->data['name'] = null;
        $this->getters['name'] = 'getName';
        $this->setters['name'] = 'setName';
        $this->data['parent_id'] = null;
        $this->getters['parent_id'] = 'getParentId';
        $this->setters['parent_id'] = 'setParentId';
        $this->data['allowed_children'] = null;
        $this->getters['allowed_children'] = 'getAllowedChildren';
        $this->setters['allowed_children'] = 'setAllowedChildren';
        $this->data['definition'] = null;
        $this->getters['definition'] = 'getDefinition';
        $this->setters['definition'] = 'setDefinition';
        $this->data['icon'] = null;
        $this->getters['icon'] = 'getIcon';
        $this->setters['icon'] = 'setIcon';

        // Foreign keys:
        $this->getters['Parent'] = 'getParent';
        $this->setters['Parent'] = 'setParent';
    }
    /**
    * Get the value of Id / id.
    *
    * @return int
    */
    public function getId()
    {
        $rtn = $this->data['id'];

        return $rtn;
    }

    /**
    * Get the value of Name / name.
    *
    * @return string
    */
    public function getName()
    {
        $rtn = $this->data['name'];

        return $rtn;
    }

    /**
    * Get the value of ParentId / parent_id.
    *
    * @return int
    */
    public function getParentId()
    {
        $rtn = $this->data['parent_id'];

        return $rtn;
    }

    /**
    * Get the value of AllowedChildren / allowed_children.
    *
    * @return string
    */
    public function getAllowedChildren()
    {
        $rtn = $this->data['allowed_children'];

        return $rtn;
    }

    /**
    * Get the value of Definition / definition.
    *
    * @return string
    */
    public function getDefinition()
    {
        $rtn = $this->data['definition'];

        return $rtn;
    }

    /**
    * Get the value of Icon / icon.
    *
    * @return string
    */
    public function getIcon()
    {
        $rtn = $this->data['icon'];

        return $rtn;
    }


    /**
    * Set the value of Id / id.
    *
    * Must not be null.
    * @param $value int
    */
    public function setId($value)
    {
        $this->validateInt('Id', $value);
        $this->validateNotNull('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;
        $this->setModified('id');
    }

    /**
    * Set the value of Name / name.
    *
    * Must not be null.
    * @param $value string
    */
    public function setName($value)
    {
        $this->validateString('Name', $value);
        $this->validateNotNull('Name', $value);

        if ($this->data['name'] === $value) {
            return;
        }

        $this->data['name'] = $value;
        $this->setModified('name');
    }

    /**
    * Set the value of ParentId / parent_id.
    *
    * @param $value int
    */
    public function setParentId($value)
    {
        $this->validateInt('ParentId', $value);

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
    * Set the value of AllowedChildren / allowed_children.
    *
    * @param $value string
    */
    public function setAllowedChildren($value)
    {
        $this->validateString('AllowedChildren', $value);

        if ($this->data['allowed_children'] === $value) {
            return;
        }

        $this->data['allowed_children'] = $value;
        $this->setModified('allowed_children');
    }

    /**
    * Set the value of Definition / definition.
    *
    * Must not be null.
    * @param $value string
    */
    public function setDefinition($value)
    {
        $this->validateString('Definition', $value);
        $this->validateNotNull('Definition', $value);

        if ($this->data['definition'] === $value) {
            return;
        }

        $this->data['definition'] = $value;
        $this->setModified('definition');
    }

    /**
    * Set the value of Icon / icon.
    *
    * @param $value string
    */
    public function setIcon($value)
    {
        $this->validateString('Icon', $value);

        if ($this->data['icon'] === $value) {
            return;
        }

        $this->data['icon'] = $value;
        $this->setModified('icon');
    }
    /**
    * Get the ContentType model for this ContentType by Id.
    *
    * @uses \Octo\Pages\Store\ContentTypeStore::getById()
    * @uses \Octo\Pages\Model\ContentType
    * @return \Octo\Pages\Model\ContentType
    */
    public function getParent()
    {
        $key = $this->getParentId();

        if (empty($key)) {
            return null;
        }

        return Factory::getStore('ContentType', 'Octo\Pages')->getById($key);
    }

    /**
    * Set Parent - Accepts an ID, an array representing a ContentType or a ContentType model.
    *
    * @param $value mixed
    */
    public function setParent($value)
    {
        // Is this an instance of ContentType?
        if ($value instanceof \Octo\Pages\Model\ContentType) {
            return $this->setParentObject($value);
        }

        // Is this an array representing a ContentType item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setParentId($value['id']);
        }

        // Is this a scalar value representing the ID of this foreign key?
        return $this->setParentId($value);
    }

    /**
    * Set Parent - Accepts a ContentType model.
    *
    * @param $value \Octo\Pages\Model\ContentType
    */
    public function setParentObject(\Octo\Pages\Model\ContentType $value)
    {
        return $this->setParentId($value->getId());
    }
}
