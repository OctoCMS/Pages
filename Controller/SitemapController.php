<?php

namespace Octo\Pages\Controller;

use b8\Config;
use Octo\Controller;
use Octo\Pages\Model\Page;
use Octo\Store;

class SitemapController extends Controller
{

    public function index()
    {

    }

    public function xml()
    {
        /** @var \Octo\Pages\Store\PageStore $pageStore */
        $pageStore = Store::get('Page');

        $home = $pageStore->getHomepage();

        $sitemap = [];
        $this->addPageToXml($home, $sitemap);

        header('Content-Type: application/xml');

        print '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        print '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($sitemap as $url) {
            print '    <url>' . PHP_EOL;
            print '        <loc>' . $url['loc'] . '</loc>' . PHP_EOL;
            print '        <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            print '        <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            print '    </url>' . PHP_EOL;
        }

        print '</urlset>';
        die;
    }

    protected function addPageToXml(Page $page, array &$sitemap, $priority = 1)
    {
        /** @var \Octo\Pages\Store\PageStore $pageStore */
        $pageStore = Store::get('Page');
        $now = new \DateTime();

        // Skip not yet published pages:
        if (!is_null($page->getPublishDate()) && $page->getPublishDate() > $now) {
            return;
        }

        // Skip expired pages:
        if (!is_null($page->getExpiryDate()) && $page->getExpiryDate() <= $now) {
            return;
        }

        $sitemap[] = [
            'loc' => Config::getInstance()->get('site.url') . $page->getUri(),
            'lastmod' => $page->getCurrentVersion()->getUpdatedDate()->format('Y-m-d'),
            'priority' => ($priority < 0.1) ? 0.1 : $priority,
        ];

        if ($page->hasChildren()) {
            $childPriority = $priority - 0.1;

            foreach ($pageStore->getByParentId($page->getId()) as $child) {
                $this->addPageToXml($child, $sitemap, $childPriority);
            }
        }
    }
}