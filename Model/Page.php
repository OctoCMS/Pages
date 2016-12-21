<?php

/**
 * Page model for table: page
 */

namespace Octo\Pages\Model;

use Octo;
use Octo\Store;
use Octo\Utilities\StringUtilities;

/**
 * Page Model
 * @uses Octo\Pages\Model\Base\PageBaseBase
 */
class Page extends Base\PageBase implements Octo\System\Searchable
{
	public function __construct($initialData = array())
    {
        parent::__construct($initialData);
        $this->getters['hasChildren'] = 'hasChildren';
        $this->getters['isLocked'] = 'getIsLocked';
        $this->getters['latestVersion'] = 'getLatestVersion';
    }
    
    public function getSearchId()
    {
        return $this->getId();
    }
    
    public function getSearchContent() : string
    {
        $rtn = '';

        if ($this->getPublishDate() > (new \DateTime())) {
            return $rtn;
        }

        $current = $this->getCurrentVersion();

        if (!$current) {
            return $rtn;
        }

        $rtn .= $current->getTitle() . ' ';
        $rtn .= $current->getDescription() . ' ';
        $rtn .= $current->getMetaDescription() . ' ';

        $content = $current->getContentItem()->getContent();

        if (is_array($content)) {
            foreach ($content as $item) {
                if (is_string($item)) {
                    $rtn .= $item . ' ';
                    continue;
                }

                if (is_array($item)) {
                    foreach ($item as $value) {
                        if (is_string($value)) {
                            $rtn .= $value . ' ';
                        }
                    }
                }
            }
        }
        
        return $rtn;
    }

    public function __get($key)
    {
        return $this->getVariable($key);
    }

    public function __exists($key)
    {
        if (!is_null($this->getVariable($key))) {
            return true;
        }

        return false;
    }

    public function getVariable($key, $recursive = false)
    {
        // Try local variables:
        if (array_key_exists($key, $this->getters)) {
            $getter = $this->getters[$key];
            return $this->{$getter}();
        }

        $rtn = $this->getCurrentVersion()->getVariable($key, false);
        if (!is_null($rtn)) {
            return $rtn;
        }

        // Try and get from parent page:
        if ($recursive && $this->getParentId()) {
            return $this->getParent()->getVariable($key, true);
        }

        return null;
    }

    public function hasContent($key)
    {
        return $this->getCurrentVersion()->hasContent($key);
    }

    public function hasChildren()
    {
        /** @var Octo\Pages\Store\PageStore $store */
        $store = Store::get('Page');
        $count = $store->getChildrenCount($this);
        return $count ? true : false;
    }

    public function getChildren()
    {
        /** @var Octo\Pages\Store\PageStore $store */
        $store = Store::get('Page');
        return $store->getByParentId($this->getId());
    }

    public function generateId()
    {
        $this->setId(substr(sha1(uniqid('', true)), 0, 5));
        $this->setUri('temporary-' . $this->getId());
    }

    public function generateUri()
    {
        if (is_null($this->getParentId())) {
            $this->setUri('/');
        } else {
            $uri = $this->getParent()->getUri();

            if (substr($uri, -1) != '/') {
                $uri .= '/';
            }

            $uri .= StringUtilities::generateSlug($this->getCurrentVersion()->getShortTitle());

            $existingPage = Store::get('Page')->getByUri($uri);

            if (!empty($existingPage) && $existingPage->getId() != $this->getId()) {
                $uri .= '-' . $this->getId();
            }

            $this->setUri($uri);
        }
    }

    /**
     * @return PageVersion|null
     * @throws \Block8\Database\Exception
     */
    public function getLatestVersion()
    {
        /** @var Octo\Pages\Store\PageStore $store */
        return Store::get('Page')->getLatestVersion($this);
    }

    public function getIsLocked()
    {
        $latest = $this->getLatestVersion();

        if ($latest->getUserId() == $_SESSION['user_id']) {
            return false;
        }

        if ($latest->getUpdatedDate() > new \DateTime('-1 min')) {
            return true;
        }

        return false;
    }

    /**
     * @return Page[]
     */
    public function getAncestors()
    {
        $ancestors = [];
        $ancestors[] = $this;
        $currentPage = $this;

        while ($currentPage->getParentId()) {
            $parent = $currentPage->getParent();
            $ancestors[] = $parent;
            $currentPage = $parent;
        }

        return array_reverse($ancestors);
    }

    public function getAncestor($level) {
        $ancestors = $this->getAncestors();

        if (array_key_exists($level, $ancestors)) {
            return $ancestors[$level];
        }
    }
}
