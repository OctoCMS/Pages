<?php

/**
 * Page store for table: page
 */

namespace Octo\Pages\Store;

use Octo;
use b8\Database;
use b8\Database\Query;
use Octo\Pages\Model\Page;
use Octo\Pages\Model\PageVersion;

/**
 * Page Store
 * @uses Octo\Pages\Store\Base\PageStoreBase
 */
class PageStore extends Base\PageStoreBase
{
	/**
     * Get the homepage for the current site.
     * @return Page
     */
    public function getHomepage()
    {
        return $this->where('uri', '/')->first();
    }

    public function getAll()
    {
        return $this->find()->get();
    }

    /**
     * Get the total number of pages in the system.
     * @return int
     */
    public function getTotal() : int
    {
        return $this->find()->count();
    }

    /**
     * Get the number of child pages for a given page.
     * @param Page $page
     * @return int
     */
    public function getChildrenCount(Page $page)
    {
        return $this->where('parent_id', $page->getId())->count();
    }

    /**
     * Get the latest version of a given page from the database.
     * @param Page $page
     * @return PageVersion
     */
    public function getLatestVersion(Page $page, $useConnection = 'read')
    {
        return PageVersion::Store()->where('page_id', $page->getId())->order('version', 'DESC')->first();
    }

    public function getParentPageOptions($options = null, $parent = null, $prefix = '')
    {
        if (is_null($options)) {
            $parent = $this->getHomepage();

            if (is_null($parent)) {
                return [0 => 'Create Site Homepage'];
            }

            $options = [$parent->getId() => $parent->getCurrentVersion()->getTitle()];
        }

        $children = $this->getByParentId($parent->getId());
        foreach ($children as $page) {
            if ($page->getCurrentVersionId()) {
                $options[$page->getId()] = $prefix  . $page->getCurrentVersion()->getTitle();
                $options = $this->getParentPageOptions($options, $page, '--' . $prefix);
            }
        }

        return $options;
    }

    public function getLatest($limit = 10)
    {
        return $this->find()
            ->join('page_version', 'page_version.id', 'page.current_version_id')
            ->order('page_version.id', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function search($query)
    {
        return $this->find()
            ->join('page_version', 'page_version.id', 'page.current_version_id')
            ->rawWhere('(title LIKE \'%'.$query.'%\' OR short_title LIKE \'%'.$query.'%\' OR page.id = \''.$query.'\')')
            ->get();
    }

    public function getModelsToIndex()
    {
        return $this->getAll();
    }
}
