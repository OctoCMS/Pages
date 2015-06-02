<?php

/**
 * PageVersion model for table: page_version */

namespace Octo\Pages\Model;

use Octo;

/**
 * PageVersion Model
 */
class PageVersion extends Octo\Model
{
    use Base\PageVersionBase;

    public function __get($key)
    {
        return $this->getVariable($key);
    }

    public function getVariable($key)
    {
        // Try page properties:
        if (array_key_exists($key, $this->getters)) {
            $getter = $this->getters[$key];
            return $this->{$getter}();
        }

        // Try page content:
        $content = json_decode($this->getContentItem()->getContent(), true);

        if (!empty($content[$key])) {
            return $content[$key];
        }

        // Try and get from parent page:
        if ($this->getPage()->getParentId()) {
            return $this->getPage()->getParent()->getCurrentVersion()->getVariable($key);
        }

        return null;
    }

    public function hasContent($key)
    {
        $content = json_decode($this->getContentItem()->getContent(), true);
        return !empty($content[$key]);
    }
}
