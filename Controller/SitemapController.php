<?php

namespace Octo\Pages\Controller;

use b8\Cache;
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

        $cache = Cache::getCache();
        $key = Config::getInstance()->get('site.namespace') . '_sitemap';

        if ($cache->contains($key)) {
            $xml = $cache->get($key);
        } else {
            $xml = $this->getXmlSitemap();

            // Cache the sitemap XML for 30 minutes:
            $cache->set($key, $xml, 1800);
        }

        header('Content-Type: application/xml');
        die($xml);
    }

    protected function getXmlSitemap()
    {
        /** @var \Octo\Pages\Store\PageStore $pageStore */
        $pageStore = Store::get('Page');

        $home = $pageStore->getHomepage();

        $sitemap = [];
        $this->addPageToXml($home, $sitemap);


        $output = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($sitemap as $url) {
            $output .= '    <url>' . PHP_EOL;
            $output .= '        <loc>' . $url['loc'] . '</loc>' . PHP_EOL;
            $output .= '        <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $output .= '        <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $output .= '    </url>' . PHP_EOL;
        }

        $output .= '</urlset>';

        return $output;
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