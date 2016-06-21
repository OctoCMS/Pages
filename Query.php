<?php

namespace Octo\Pages;

use Octo\Pages\Model\Page;
use Octo\Pages\Store\PageStore;
use Octo\Store;

class Query
{
    /**
     * @var PageStore
     */
    protected $store;
    protected $query;
    protected $ordered = false;
    protected $perPage;
    protected $currentPage;

    public function __construct()
    {
        $this->store = Store::get('Page');
        $this->query = $this->store->find()
            ->rawWhere('(publish_date IS NULL OR publish_date <= NOW())')
            ->rawWhere('(expiry_date IS NULL OR expiry_date > NOW())');
    }

    public function parent(Page $page)
    {
        $this->query->where('parent_id', $page->getId());
        return $this;
    }

    public function order($column, $direction = 'ASC')
    {
        $this->ordered = true;
        $this->query->order($column, $direction);
        return $this;
    }

    public function paginate(int $currentPage, int $perPage)
    {
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->query->limit($this->perPage);
        $this->query->offset(($this->currentPage - 1) * $this->perPage);

        return $this;
    }

    public function get()
    {
        if (!$this->ordered) {
            $this->order('position', 'ASC');
            $this->order('publish_date', 'DESC');
        }

        return $this->query->get();
    }

    public function pageCount()
    {
        return ceil($this->query->count() / $this->perPage);
    }
}