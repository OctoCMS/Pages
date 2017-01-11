<?php

/**
 * PageVersion base model for table: page_version
 */

namespace Octo\Pages\Model\Base;

use DateTime;
use Block8\Database\Query;
use Octo\Model;
use Octo\Store;
use Octo\Pages\Model\PageVersion;
use Octo\Pages\Store\PageVersionStore;

/**
 * PageVersion Base Model
 */
abstract class PageVersionBase extends Model
{
    protected $table = 'page_version';
    protected $model = 'PageVersion';
    protected $data = [
        'id' => null,
        'page_id' => null,
        'version' => null,
        'title' => null,
        'short_title' => null,
        'description' => null,
        'meta_description' => null,
        'content_item_id' => null,
        'user_id' => null,
        'updated_date' => null,
        'template' => null,
        'image_id' => null,
    ];

    protected $getters = [
        'id' => 'getId',
        'page_id' => 'getPageId',
        'version' => 'getVersion',
        'title' => 'getTitle',
        'short_title' => 'getShortTitle',
        'description' => 'getDescription',
        'meta_description' => 'getMetaDescription',
        'content_item_id' => 'getContentItemId',
        'user_id' => 'getUserId',
        'updated_date' => 'getUpdatedDate',
        'template' => 'getTemplate',
        'image_id' => 'getImageId',
        'ContentItem' => 'getContentItem',
        'Page' => 'getPage',
        'Image' => 'getImage',
        'User' => 'getUser',
    ];

    protected $setters = [
        'id' => 'setId',
        'page_id' => 'setPageId',
        'version' => 'setVersion',
        'title' => 'setTitle',
        'short_title' => 'setShortTitle',
        'description' => 'setDescription',
        'meta_description' => 'setMetaDescription',
        'content_item_id' => 'setContentItemId',
        'user_id' => 'setUserId',
        'updated_date' => 'setUpdatedDate',
        'template' => 'setTemplate',
        'image_id' => 'setImageId',
        'ContentItem' => 'setContentItem',
        'Page' => 'setPage',
        'Image' => 'setImage',
        'User' => 'setUser',
    ];

    /**
     * Return the database store for this model.
     * @return PageVersionStore
     */
    public static function Store() : PageVersionStore
    {
        return PageVersionStore::load();
    }

    /**
     * Get PageVersion by primary key: id
     * @param int $id
     * @return PageVersion|null
     */
    public static function get(int $id) : ?PageVersion
    {
        return self::Store()->getById($id);
    }

    /**
     * @throws \Exception
     * @return PageVersion
     */
    public function save() : PageVersion
    {
        $rtn = self::Store()->save($this);

        if (empty($rtn)) {
            throw new \Exception('Failed to save PageVersion');
        }

        if (!($rtn instanceof PageVersion)) {
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
     * Get the value of PageId / page_id
     * @return string
     */

     public function getPageId() : string
     {
        $rtn = $this->data['page_id'];

        return $rtn;
     }
    
    /**
     * Get the value of Version / version
     * @return int
     */

     public function getVersion() : int
     {
        $rtn = $this->data['version'];

        return $rtn;
     }
    
    /**
     * Get the value of Title / title
     * @return string
     */

     public function getTitle() : ?string
     {
        $rtn = $this->data['title'];

        return $rtn;
     }
    
    /**
     * Get the value of ShortTitle / short_title
     * @return string
     */

     public function getShortTitle() : ?string
     {
        $rtn = $this->data['short_title'];

        return $rtn;
     }
    
    /**
     * Get the value of Description / description
     * @return string
     */

     public function getDescription() : ?string
     {
        $rtn = $this->data['description'];

        return $rtn;
     }
    
    /**
     * Get the value of MetaDescription / meta_description
     * @return string
     */

     public function getMetaDescription() : ?string
     {
        $rtn = $this->data['meta_description'];

        return $rtn;
     }
    
    /**
     * Get the value of ContentItemId / content_item_id
     * @return string
     */

     public function getContentItemId() : ?string
     {
        $rtn = $this->data['content_item_id'];

        return $rtn;
     }
    
    /**
     * Get the value of UserId / user_id
     * @return int
     */

     public function getUserId() : ?int
     {
        $rtn = $this->data['user_id'];

        return $rtn;
     }
    
    /**
     * Get the value of UpdatedDate / updated_date
     * @return DateTime
     */

     public function getUpdatedDate() : DateTime
     {
        $rtn = $this->data['updated_date'];

        if (!empty($rtn)) {
            $rtn = new DateTime($rtn);
        }

        return $rtn;
     }
    
    /**
     * Get the value of Template / template
     * @return string
     */

     public function getTemplate() : string
     {
        $rtn = $this->data['template'];

        return $rtn;
     }
    
    /**
     * Get the value of ImageId / image_id
     * @return string
     */

     public function getImageId() : ?string
     {
        $rtn = $this->data['image_id'];

        return $rtn;
     }
    
    
    /**
     * Set the value of Id / id
     * @param $value int
     * @return PageVersion
     */
    public function setId(int $value) : PageVersion
    {

        if ($this->data['id'] !== $value) {
            $this->data['id'] = $value;
            $this->setModified('id');
        }

        return $this;
    }
    
    /**
     * Set the value of PageId / page_id
     * @param $value string
     * @return PageVersion
     */
    public function setPageId(string $value) : PageVersion
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['page_id'] !== $value) {
            $this->data['page_id'] = $value;
            $this->setModified('page_id');
        }

        return $this;
    }
    
    /**
     * Set the value of Version / version
     * @param $value int
     * @return PageVersion
     */
    public function setVersion(int $value) : PageVersion
    {

        if ($this->data['version'] !== $value) {
            $this->data['version'] = $value;
            $this->setModified('version');
        }

        return $this;
    }
    
    /**
     * Set the value of Title / title
     * @param $value string
     * @return PageVersion
     */
    public function setTitle(?string $value) : PageVersion
    {

        if ($this->data['title'] !== $value) {
            $this->data['title'] = $value;
            $this->setModified('title');
        }

        return $this;
    }
    
    /**
     * Set the value of ShortTitle / short_title
     * @param $value string
     * @return PageVersion
     */
    public function setShortTitle(?string $value) : PageVersion
    {

        if ($this->data['short_title'] !== $value) {
            $this->data['short_title'] = $value;
            $this->setModified('short_title');
        }

        return $this;
    }
    
    /**
     * Set the value of Description / description
     * @param $value string
     * @return PageVersion
     */
    public function setDescription(?string $value) : PageVersion
    {

        if ($this->data['description'] !== $value) {
            $this->data['description'] = $value;
            $this->setModified('description');
        }

        return $this;
    }
    
    /**
     * Set the value of MetaDescription / meta_description
     * @param $value string
     * @return PageVersion
     */
    public function setMetaDescription(?string $value) : PageVersion
    {

        if ($this->data['meta_description'] !== $value) {
            $this->data['meta_description'] = $value;
            $this->setModified('meta_description');
        }

        return $this;
    }
    
    /**
     * Set the value of ContentItemId / content_item_id
     * @param $value string
     * @return PageVersion
     */
    public function setContentItemId(?string $value) : PageVersion
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['content_item_id'] !== $value) {
            $this->data['content_item_id'] = $value;
            $this->setModified('content_item_id');
        }

        return $this;
    }
    
    /**
     * Set the value of UserId / user_id
     * @param $value int
     * @return PageVersion
     */
    public function setUserId(?int $value) : PageVersion
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['user_id'] !== $value) {
            $this->data['user_id'] = $value;
            $this->setModified('user_id');
        }

        return $this;
    }
    
    /**
     * Set the value of UpdatedDate / updated_date
     * @param $value DateTime
     * @return PageVersion
     */
    public function setUpdatedDate($value) : PageVersion
    {
        $this->validateDate('UpdatedDate', $value);

        if ($this->data['updated_date'] !== $value) {
            $this->data['updated_date'] = $value;
            $this->setModified('updated_date');
        }

        return $this;
    }
    
    /**
     * Set the value of Template / template
     * @param $value string
     * @return PageVersion
     */
    public function setTemplate(string $value) : PageVersion
    {

        if ($this->data['template'] !== $value) {
            $this->data['template'] = $value;
            $this->setModified('template');
        }

        return $this;
    }
    
    /**
     * Set the value of ImageId / image_id
     * @param $value string
     * @return PageVersion
     */
    public function setImageId(?string $value) : PageVersion
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['image_id'] !== $value) {
            $this->data['image_id'] = $value;
            $this->setModified('image_id');
        }

        return $this;
    }
    
    
    /**
     * Get the ContentItem model for this  by Id.
     *
     * @uses \Octo\System\Store\ContentItemStore::getById()
     * @uses \Octo\System\Model\ContentItem
     * @return \Octo\System\Model\ContentItem
     */
    public function getContentItem()
    {
        $key = $this->getContentItemId();

        if (empty($key)) {
           return null;
        }

        return Store::get('ContentItem')->getById($key);
    }

    /**
     * Set ContentItem - Accepts an ID, an array representing a ContentItem or a ContentItem model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setContentItem($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setContentItemId($value);
        }

        // Is this an instance of ContentItem?
        if (is_object($value) && $value instanceof \Octo\System\Model\ContentItem) {
            return $this->setContentItemObject($value);
        }

        // Is this an array representing a ContentItem item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setContentItemId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for ContentItem.');
    }

    /**
     * Set ContentItem - Accepts a ContentItem model.
     *
     * @param $value \Octo\System\Model\ContentItem
     */
    public function setContentItemObject(\Octo\System\Model\ContentItem $value)
    {
        return $this->setContentItemId($value->getId());
    }

    /**
     * Get the Page model for this  by Id.
     *
     * @uses \Octo\Pages\Store\PageStore::getById()
     * @uses \Octo\Pages\Model\Page
     * @return \Octo\Pages\Model\Page
     */
    public function getPage()
    {
        $key = $this->getPageId();

        if (empty($key)) {
           return null;
        }

        return Store::get('Page')->getById($key);
    }

    /**
     * Set Page - Accepts an ID, an array representing a Page or a Page model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setPage($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setPageId($value);
        }

        // Is this an instance of Page?
        if (is_object($value) && $value instanceof \Octo\Pages\Model\Page) {
            return $this->setPageObject($value);
        }

        // Is this an array representing a Page item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setPageId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for Page.');
    }

    /**
     * Set Page - Accepts a Page model.
     *
     * @param $value \Octo\Pages\Model\Page
     */
    public function setPageObject(\Octo\Pages\Model\Page $value)
    {
        return $this->setPageId($value->getId());
    }

    /**
     * Get the File model for this  by Id.
     *
     * @uses \Octo\File\Store\FileStore::getById()
     * @uses \Octo\File\Model\File
     * @return \Octo\File\Model\File
     */
    public function getImage()
    {
        $key = $this->getImageId();

        if (empty($key)) {
           return null;
        }

        return Store::get('File')->getById($key);
    }

    /**
     * Set Image - Accepts an ID, an array representing a File or a File model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setImage($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setImageId($value);
        }

        // Is this an instance of Image?
        if (is_object($value) && $value instanceof \Octo\File\Model\File) {
            return $this->setImageObject($value);
        }

        // Is this an array representing a File item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setImageId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for Image.');
    }

    /**
     * Set Image - Accepts a File model.
     *
     * @param $value \Octo\File\Model\File
     */
    public function setImageObject(\Octo\File\Model\File $value)
    {
        return $this->setImageId($value->getId());
    }

    /**
     * Get the User model for this  by Id.
     *
     * @uses \Octo\System\Store\UserStore::getById()
     * @uses \Octo\System\Model\User
     * @return \Octo\System\Model\User
     */
    public function getUser()
    {
        $key = $this->getUserId();

        if (empty($key)) {
           return null;
        }

        return Store::get('User')->getById($key);
    }

    /**
     * Set User - Accepts an ID, an array representing a User or a User model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setUser($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setUserId($value);
        }

        // Is this an instance of User?
        if (is_object($value) && $value instanceof \Octo\System\Model\User) {
            return $this->setUserObject($value);
        }

        // Is this an array representing a User item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setUserId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for User.');
    }

    /**
     * Set User - Accepts a User model.
     *
     * @param $value \Octo\System\Model\User
     */
    public function setUserObject(\Octo\System\Model\User $value)
    {
        return $this->setUserId($value->getId());
    }


    public function Pages() : Query
    {
        return Store::get('Page')->where('current_version_id', $this->data['id']);
    }
}
