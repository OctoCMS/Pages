<?php

namespace Octo\Pages;

use b8\Config;
use b8\Http\Request;
use b8\Http\Response;
use Octo\Block;
use Octo\Event;
use Octo\Template;
use Octo\Pages\Model\Page;
use Octo\Pages\Model\PageVersion;

class Renderer
{
    /**
     * @var Model\Page
     */
    protected $page;

    /**
     * @var Model\PageVersion
     */
    protected $version;

    /**
     * @var \Octo\Template
     */
    protected $template;

    /**
     * @var \b8\Http\Request
     */
    protected $request;

    /**
     * @var \b8\Config
     */
    protected $config;

    /**
     * @param Page $page
     * @param PageVersion $version
     * @param Request $request
     */
    public function __construct(Page $page, PageVersion $version, Request $request)
    {
        $this->page = $page;
        $this->version = $version;
        $this->request = $request;
        $this->template = new Template($version->getTemplate());
        $this->config = Config::getInstance();
    }

    public function render()
    {
        $content = $this->processContent();

        $this->template->page = $this->page;
        $this->template->version = $this->version;
        $this->template->request = $this->request;
        $this->template->config = $this->config;
        $this->template->content = $content;

        return $this->template->render();
    }

    protected function processContent()
    {
        $rtn = [];
        $definition = $this->page->getContentType()->getFullDefinition();

        foreach ($definition as $tab) {
            if (empty($tab['properties']) || !is_array($tab['properties'])) {
                continue;
            }

            foreach ($tab['properties'] as $prop => $value) {
                $content = $this->version->getVariable($prop, $value['inherited']);

                if (empty($content)) {
                    $content = [];
                }

                if (Block::exists($value['type'])) {
                    $block = Block::create($value['type'], $content);
                    $block->setPage($this->page);
                    $block->setPageVersion($this->version);
                    $block->setRequest($this->request);

                    $rtn[$prop] = $block->render();
                }
            }
        }

        return $rtn;
    }
}