<?php

namespace Octo\Pages\Block;

use b8\Database;
use Octo\Block;
use Octo\Pages\Model\Page;
use Octo\Store;
use Octo\Html\Template;

class Navigation extends Block
{
    /**
     * @var \Octo\Pages\Store\PageStore
     */
    protected $pageStore;

    public static function getInfo()
    {
        return ['title' => 'Navigation'];
    }

    public function init()
    {
        $this->pageStore = Store::get('Page');
    }

    public function renderNow()
    {
        $this->ancestors = $this->getAncestors();

        $this->allChildren = false;
        $start = count($this->ancestors);
        $maxDepth = 1;

        if (array_key_exists('allChildren', $this->templateParams)) {
            $this->allChildren = $this->templateParams['allChildren'] ? true : false;
        }

        if (array_key_exists('start', $this->templateParams)) {
            $start = (int)$this->templateParams['start'];
        }

        if (array_key_exists('depth', $this->templateParams)) {
            $maxDepth = (int)$this->templateParams['depth'];
        }

        if (isset($this->ancestors[$start - 1])) {
            $items = $this->buildTree($this->ancestors[$start - 1], 0, $maxDepth);

            if (isset($items['children'])) {
                $this->view->items = $items['children'];
            } else {
                $this->view->items = [];
            }

        }
    }

    protected function getAncestors()
    {
        $rtn = [$this->page];
        $page = $this->page;

        while ($page->getParentId()) {
            $page = $page->getParent();
            $rtn[] = $page;
        }

        return array_reverse($rtn);
    }

    protected function getChildren(Page $page)
    {
        $options = [
            'order' => [
                ['position', 'ASC'],
            ]
        ];

        $children = $this->pageStore->getByParentId($page->getId(), $options);

        if (count($children)) {
            return $children;
        }

        return null;
    }

    protected function buildTree(Page $item, $depth, $maxDepth)
    {
        $isAncestor = $this->isAncestor($item);

        $rtn = [];
        $rtn['uri'] = $item->getUri();
        $rtn['title'] = $item->getCurrentVersion()->getShortTitle();
        $rtn['active'] = $isAncestor;

        if ($item->getId() == $this->page->getId()) {
            $rtn['current'] = true;
        }

        if ($depth == $maxDepth || (!$this->allChildren && !$isAncestor)) {
            return $rtn;
        }

        $children = $this->getChildren($item);

        if (!is_null($children)) {
            $rtn['children'] = [];

            foreach ($children as $child) {
                $rtn['children'][] = $this->buildTree($child, $depth + 1, $maxDepth);
            }
        }

        return $rtn;
    }

    protected function isAncestor(Page $page)
    {
        foreach ($this->ancestors as $ancestor) {
            if ($page->getId() == $ancestor->getId()) {
                return true;
            }
        }

        return false;
    }
}
