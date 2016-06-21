<?php

/**
 * PageVersion model for table: page_version */

namespace Octo\Pages\Model;

use Octo;

/**
 * PageVersion Model
 */
class PageVersion extends Base\PageVersionBase
{
	public function __get($key)
    {
        return $this->getVariable($key);
    }

    public function getVariable($key, $recursive = false)
    {
        // Try page properties:
        if (array_key_exists($key, $this->getters)) {
            $getter = $this->getters[$key];
            return $this->{$getter}();
        }

        // Try page content:
        $content = $this->getContentItem()->getContent();

        if (!empty($content[$key])) {
            return $content[$key];
        }

        // Try and get from parent page:
        if ($recursive && $this->getPage()->getParentId()) {
            return $this->getPage()->getParent()->getCurrentVersion()->getVariable($key, true);
        }

        return null;
    }

    public function hasContent($key)
    {
        $content = $this->getContentItem()->getContent();
        return !empty($content[$key]);
    }
}
