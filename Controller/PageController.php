<?php

namespace Octo\Pages\Controller;

use Exception;
use b8\Exception\HttpException\NotFoundException;
use b8\Exception\HttpException;
use Octo\Block;
use Octo\BlockManager;
use Octo\Controller;
use Octo\Event;
use Octo\Pages\Model\Page;
use Octo\Pages\Model\PageVersion;
use Octo\Pages\Renderer;
use Octo\System\Model\ContentItem;
use Octo\Store;
use Octo\Html\Template;

class PageController extends Controller
{
    /**
     * @var \Octo\Pages\Store\PageStore
     */
    protected $pageStore;

    /**
     * @var \Octo\Pages\Store\PageVersionStore
     */
    protected $versionStore;

    /**
     * @var array
     */
    protected $content;

    /**
     * @var \Octo\Pages\Model\Page
     */
    protected $page;

    /**
     * @var \Octo\Pages\Model\PageVersion
     */
    protected $version;

    /**
     * @var string
     */
    protected $uriExtension;

    protected $blockManager;

    public $breadcrumb = [];

    public function init()
    {
        $this->pageStore = Store::get('Page');
        $this->versionStore = Store::get('PageVersion');

        Event::getEventManager()->registerListener('PublicTemplateLoaded', function (Template $template) {
            $template->addFunction('getPages', function ($parentId, $limit = 15) {
                return $this->getPages($parentId, $limit);
            });
        });
    }

    public function view()
    {
        $path = $this->request->getPath();

        // Try and load the page:
        $this->page = $this->pageStore->getUriBestMatch($path);

        if (empty($this->page) || !($this->page instanceof Page)) {
            throw new HttpException\NotFoundException('No page found.');
        }

        try {
            $renderer = new Renderer($this->page, $this->page->getCurrentVersion(), $this->request);
            $output = $renderer->render();
        } catch (NotFoundException $e) {
            throw new NotFoundException('Page not found: ' . $path, $e);
        } catch (Exception $e) {
            throw $e;
        }

        return $output;
    }

    public function preview($pageId)
    {
        $versionId = $this->getParam('version', null);
        $this->page = $this->pageStore->getById($pageId);

        if (is_null($versionId)) {
            $version = $this->pageStore->getLatestVersion($this->page);
        } else {
            $version = $this->versionStore->getById($versionId);
        }

        $renderer = new Renderer($this->page, $version, $this->request);
        return $renderer->render();
    }

    protected function getPages($parentId, $limit = 15)
    {
        $rtn = $this->pageStore->getByParentId($parentId, ['order' => [['position', 'ASC']], 'limit' => $limit]);

        if (empty($rtn)) {
            $rtn = [];
        }

        return $rtn;
    }
}
