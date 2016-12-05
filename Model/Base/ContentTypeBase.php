<?php

/**
 * ContentType base model for table: content_type
 */

namespace Octo\Pages\Model\Base;

use DateTime;
use Octo\Model;
use Octo\Store;

/**
 * ContentType Base Model
 */
class ContentTypeBase extends Model
{
    protected function init()
    {
        $this->table = 'content_type';
        $this->model = 'ContentType';

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
        
        $this->data['allowed_templates'] = null;
        $this->getters['allowed_templates'] = 'getAllowedTemplates';
        $this->setters['allowed_templates'] = 'setAllowedTemplates';
        
        // Foreign keys:
        
        $this->getters['Parent'] = 'getParent';
        $this->setters['Parent'] = 'setParent';
        
    }

    
    /**
     * Get the value of Id / id
     * @return int
     */

     public function getId()
     {
        $rtn = $this->data['id'];

        return $rtn;
     }
    
    /**
     * Get the value of Name / name
     * @return string
     */

     public function getName()
     {
        $rtn = $this->data['name'];

        return $rtn;
     }
    
    /**
     * Get the value of ParentId / parent_id
     * @return int
     */

     public function getParentId()
     {
        $rtn = $this->data['parent_id'];

        return $rtn;
     }
    
    /**
     * Get the value of AllowedChildren / allowed_children
     * @return array|null
     */

     public function getAllowedChildren()
     {
        $rtn = $this->data['allowed_children'];

        $rtn = json_decode($rtn, true);

        if ($rtn === false) {
            $rtn = null;
        }

        return $rtn;
     }
    
    /**
     * Get the value of Definition / definition
     * @return array|null
     */

     public function getDefinition()
     {
        $rtn = $this->data['definition'];

        $rtn = json_decode($rtn, true);

        if ($rtn === false) {
            $rtn = null;
        }

        return $rtn;
     }
    
    /**
     * Get the value of Icon / icon
     * @return string
     */

     public function getIcon()
     {
        $rtn = $this->data['icon'];

        return $rtn;
     }
    
    /**
     * Get the value of AllowedTemplates / allowed_templates
     * @return array|null
     */

     public function getAllowedTemplates()
     {
        $rtn = $this->data['allowed_templates'];

        $rtn = json_decode($rtn, true);

        if ($rtn === false) {
            $rtn = null;
        }

        return $rtn;
     }
    
    
    /**
     * Set the value of Id / id
     * @param $value int
     */
    public function setId(int $value)
    {

        $this->validateNotNull('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;
        $this->setModified('id');
    }
    
    /**
     * Set the value of Name / name
     * @param $value string
     */
    public function setName(string $value)
    {

        $this->validateNotNull('Name', $value);

        if ($this->data['name'] === $value) {
            return;
        }

        $this->data['name'] = $value;
        $this->setModified('name');
    }
    
    /**
     * Set the value of ParentId / parent_id
     * @param $value int
     */
    public function setParentId($value)
    {


        // As this column is a foreign key, empty values should be considered null.
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
     * Set the value of AllowedChildren / allowed_children
     * @param $value array|null
     */
    public function setAllowedChildren($value)
    {
        $this->validateJson($value);


        if ($this->data['allowed_children'] === $value) {
            return;
        }

        $this->data['allowed_children'] = $value;
        $this->setModified('allowed_children');
    }
    
    /**
     * Set the value of Definition / definition
     * @param $value array|null
     */
    public function setDefinition($value)
    {
        $this->validateJson($value);


        if ($this->data['definition'] === $value) {
            return;
        }

        $this->data['definition'] = $value;
        $this->setModified('definition');
    }
    
    /**
     * Set the value of Icon / icon
     * @param $value string
     */
    public function setIcon($value)
    {



        if ($this->data['icon'] === $value) {
            return;
        }

        $this->data['icon'] = $value;
        $this->setModified('icon');
    }
    
    /**
     * Set the value of AllowedTemplates / allowed_templates
     * @param $value array|null
     */
    public function setAllowedTemplates($value)
    {
        $this->validateJson($value);


        if ($this->data['allowed_templates'] === $value) {
            return;
        }

        $this->data['allowed_templates'] = $value;
        $this->setModified('allowed_templates');
    }
    
    
    /**
     * Get the ContentType model for this  by Id.
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

        return Store::get('ContentType')->getById($key);
    }

    /**
     * Set Parent - Accepts an ID, an array representing a ContentType or a ContentType model.
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
        if (is_object($value) && $value instanceof \Octo\Pages\Model\ContentType) {
            return $this->setParentObject($value);
        }

        // Is this an array representing a ContentType item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setParentId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for Parent.');
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

    public function ContentTypes()
    {
        return Store::get('ContentType')->where('parent_id', $this->data['id']);
    }

    public function Pages()
    {
        return Store::get('Page')->where('content_type_id', $this->data['id']);
    }
}
