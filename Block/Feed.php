<?php

namespace Octo\Pages\Block;

use b8\Cache;
use b8\Config;
use b8\Form\Element\Button;
use b8\Form\Element\Text as TextInput;
use Octo\Admin\Form;
use Octo\Block;
use Octo\Page\Model\Page;
use Octo\Store;
use Octo\Template;

class Feed extends Block
{
    /**
     * @var Page
     */
    protected $pageStore;

    /**
     * @var array
     */
    protected $items = [];

    public static function getInfo()
    {
        return [
            'title' => 'RSS/Atom Feed',
            'icon' => 'rss',
            'editor' => ['\Octo\Pages\Block\Feed', 'getEditorForm']
        ];
    }

    public static function getEditorForm($item)
    {
        $form = new Form();
        $form->setId('block_' . $item['id']);

        $url = TextInput::create('url', 'Feed URL (Atom or RSS)');
        $url->setId('block_feed_url_' . $item['id']);
        $form->addField($url);

        $saveButton = new Button();
        $saveButton->setValue('Save ' . $item['name']);
        $saveButton->setClass('block-save btn btn-success');
        $form->addField($saveButton);

        if (isset($item['content']) && is_array($item['content'])) {
            $form->setValues($item['content']);
        }

        return $form;
    }

    public function renderNow()
    {
        if (isset($this->content['url'])) {
            $this->items = $this->getFeed($this->content['url']);
        }

        return $this;
    }

    public function limit($limit)
    {
        $this->items['items'] = array_slice($this->items['items'], 0, $limit);
        return $this;
    }

    public function getItem($index)
    {
        $rtn = '';

        if (isset($this->items['items'][$index])) {
            $this->view = new Template('Block/FeedItem');
            $this->view->item = $this->items['items'][$index];

            $rtn = $this->view->render();
        }

        return $rtn;
    }

    public function __toString()
    {
        $this->view->items = $this->items;
        return $this->view->render();
    }

    protected function getFeed($url)
    {
        $result = null;

        if (!empty($url)) {
            $cache = Cache::getCache();
            $hash = 'feed:' . md5($url);
            //$result = $cache->get($hash, null);

            if (is_null($result)) {
                $xml = simplexml_load_file($url);

                if (isset($xml->channel)) {
                    $result = $this->processRssFeed($xml);
                } elseif (isset($xml->title)) {
                    $result = $this->processAtomFeed($xml);
                }

                $cache->set($hash, $result, 3600);
            }
        }

        $result = !is_null($result) ? $result : ['title' => 'Feed', 'items' => []];

        return $result;
    }

    protected function processRssFeed($xml)
    {
        $rtn = [];
        $rtn['title'] = (string)$xml->channel->title;
        $rtn['items'] = [];

        foreach ($xml->channel->item as $feedItem) {
            $item = [];
            $item['title'] = trim(strip_tags((string)$feedItem->title));
            $item['description'] = trim(strip_tags((string)$feedItem->description));
            $item['uri'] = trim(strip_tags((string)$feedItem->link));

            $mediaItems = $feedItem->children('http://search.yahoo.com/mrss/');

            if (isset($mediaItems->thumbnail)) {
                $image = null;

                for ($i = 0; $i < 5; $i++) {
                    if (isset($mediaItems->thumbnail[$i])) {
                        $thumb = $mediaItems->thumbnail[$i]->attributes();

                        if (is_null($image) || $image['width'] < (int)$thumb['width']) {
                            $image = ['url' => (string)$thumb['url'], 'width' => (int)$thumb['width']];
                        }
                    }
                }

                if ($image) {
                    $item['image_url'] = trim($image['url']);
                }
            }

            $rtn['items'][] = $item;
        }

        return $rtn;
    }

    protected function processAtomFeed($xml)
    {
        $rtn = [];
        $rtn['title'] = (string)$xml->title;
        $rtn['items'] = [];

        foreach ($xml->entry as $feedItem) {
            $item = [];
            $item['title'] = trim(strip_tags((string)$feedItem->title));
            $item['description'] = trim(strip_tags((string)$feedItem->summary));

            if (!empty($feedItem->updated)) {
                $item['updated'] = new \DateTime((string)$feedItem->updated);
            }

            if (!empty($feedItem->content)) {
                $item['content'] = trim(strip_tags((string)$feedItem->content));
            }

            $item['uri'] = trim(strip_tags((string)$feedItem->link['href']));
            $rtn['items'][] = $item;
        }

        return $rtn;
    }
}
