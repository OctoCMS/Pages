<?php

/**
 * ContentType base model for table: content_type
 */

namespace Octo\Pages\Model\Base;

use DateTime;
use Block8\Database\Query;
use Octo\Model;
use Octo\Store;

use Octo\Pages\Store\ContentTypeStore;
use Octo\Pages\Model\ContentType;

/**
 * ContentType Base Model
 */
abstract class ContentTypeBase extends Model
{
    protected $table = 'content_type';
    protected $model = 'ContentType';
    protected $data = [
        'id' => null,
        'name' => 'Content',
        'parent_id' => null,
        'allowed_children' => null,
        'definition' => null,
        'icon' => null,
        'allowed_templates' => null,
    ];

    protected $getters = [
        'id' => 'getId',
        'name' => 'getName',
        'parent_id' => 'getParentId',
        'allowed_children' => 'getAllowedChildren',
        'definition' => 'getDefinition',
        'icon' => 'getIcon',
        'allowed_templates' => 'getAllowedTemplates',
        'Parent' => 'getParent',
    ];

    protected $setters = [
        'id' => 'setId',
        'name' => 'setName',
        'parent_id' => 'setParentId',
        'allowed_children' => 'setAllowedChildren',
        'definition' => 'setDefinition',
        'icon' => 'setIcon',
        'allowed_templates' => 'setAllowedTemplates',
        'Parent' => 'setParent',
    ];

    /**
     * Return the database store for this model.
     * @return ContentTypeStore
     */
    public static function Store() : ContentTypeStore
    {
        return ContentTypeStore::load();
    }

    /**
     * Get ContentType by primary key: id
     * @param int $id
     * @return ContentType|null
     */
    public static function get(int $id) : ?ContentType
    {
        return self::Store()->getById($id);
    }

    /**
     * @throws \Exception
     * @return ContentType
     */
    public function save() : ContentType
    {
        $rtn = self::Store()->save($this);

        if (empty($rtn)) {
            throw new \Exception('Failed to save ContentType');
        }

        if (!($rtn instanceof ContentType)) {
            throw new \Exception('Unexpected ' . get_class($rtn) . ' received from save.');
        }

        $this->data = $rtn->toArray();

        return $this;
    }


    /**
     * Get the value of Id / id
     * @return int
     */
     public function getId() : int
     {
        $rtn = $this->data['id'];

        return $rtn;
     }
    
    /**
     * Get the value of Name / name
     * @return string
     */
     public function getName() : string
     {
        $rtn = $this->data['name'];

        return $rtn;
     }
    
    /**
     * Get the value of ParentId / parent_id
     * @return int
     */
     public function getParentId() : ?int
     {
        $rtn = $this->data['parent_id'];

        return $rtn;
     }
    
    /**
     * Get the value of AllowedChildren / allowed_children
     * @return array
     */
     public function getAllowedChildren() : ?array
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
     * @return array
     */
     public function getDefinition() : ?array
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
     public function getIcon() : ?string
     {
        $rtn = $this->data['icon'];

        return $rtn;
     }
    
    /**
     * Get the value of AllowedTemplates / allowed_templates
     * @return array
     */
     public function getAllowedTemplates() : ?array
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
     * @return ContentType
     */
    public function setId(int $value) : ContentType
    {

        if ($this->data['id'] !== $value) {
            $this->data['id'] = $value;
            $this->setModified('id');
        }

        return $this;
    }
    
    /**
     * Set the value of Name / name
     * @param $value string
     * @return ContentType
     */
    public function setName(string $value) : ContentType
    {

        if ($this->data['name'] !== $value) {
            $this->data['name'] = $value;
            $this->setModified('name');
        }

        return $this;
    }
    
    /**
     * Set the value of ParentId / parent_id
     * @param $value int
     * @return ContentType
     */
    public function setParentId(?int $value) : ContentType
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
     * Set the value of AllowedChildren / allowed_children
     * @param $value array
     * @return ContentType
     */
    public function setAllowedChildren($value) : ContentType
    {
        $this->validateJson($value);

        if ($this->data['allowed_children'] !== $value) {
            $this->data['allowed_children'] = $value;
            $this->setModified('allowed_children');
        }

        return $this;
    }
    
    /**
     * Set the value of Definition / definition
     * @param $value array
     * @return ContentType
     */
    public function setDefinition($value) : ContentType
    {
        $this->validateJson($value);

        if ($this->data['definition'] !== $value) {
            $this->data['definition'] = $value;
            $this->setModified('definition');
        }

        return $this;
    }
    
    /**
     * Set the value of Icon / icon
     * @param $value string
     * @return ContentType
     */
    public function setIcon(?string $value) : ContentType
    {

        if ($this->data['icon'] !== $value) {
            $this->data['icon'] = $value;
            $this->setModified('icon');
        }

        return $this;
    }
    
    /**
     * Set the value of AllowedTemplates / allowed_templates
     * @param $value array
     * @return ContentType
     */
    public function setAllowedTemplates($value) : ContentType
    {
        $this->validateJson($value);

        if ($this->data['allowed_templates'] !== $value) {
            $this->data['allowed_templates'] = $value;
            $this->setModified('allowed_templates');
        }

        return $this;
    }
    

    /**
     * Get the ContentType model for this  by Id.
     *
     * @uses \Octo\Pages\Store\ContentTypeStore::getById()
     * @uses ContentType
     * @return ContentType|null
     */
    public function getParent() : ?ContentType
    {
        $key = $this->getParentId();

        if (empty($key)) {
           return null;
        }

        return ContentType::Store()->getById($key);
    }

    /**
     * Set Parent - Accepts an ID, an array representing a ContentType or a ContentType model.
     * @throws \Exception
     * @param $value mixed
     * @return ContentType
     */
    public function setParent($value) : ContentType
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setParentId($value);
        }

        // Is this an instance of Parent?
        if (is_object($value) && $value instanceof ContentType) {
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
     * @param $value ContentType
     * @return ContentType
     */
    public function setParentObject(ContentType $value) : ContentType
    {
        return $this->setParentId($value->getId());
    }
}
