<?php

namespace Octo\Pages\Event;

use b8\Http\Router;
use Octo\Event\Listener;
use Octo\Event\Manager;
use Octo\Store;
use Octo\Html\Template;

class TemplateFunctions extends Listener
{
    public function registerListeners(Manager $manager)
    {
        $manager->registerListener('PublicTemplateLoaded', function (Template $template) {
            $template->addFunction('getPages', function () {
                return call_user_func_array([$this, 'getPages'], func_get_args());
            });
        });
    }

    protected function getPages($parentId, $limit = 15)
    {
        /**
         * @var $pageStore \Octo\Pages\Store\PageStore
         */
        $pageStore = Store::get('Page');

        if (empty($parentId)) {
            return [];
        }

        $page = isset($_REQUEST['p']) ? (int)$_REQUEST['p'] : 0;

        if (!empty($page)) {
            $offset = $page * $limit;
        } else {
            $offset = 0;
        }

        $rtn = $pageStore->getByParentId($parentId, ['order' => [['position', 'ASC']], 'limit' => $limit, 'offset' => $offset]);

        // Filter out unpublished, or expired items.
        $rtn = $rtn->where(function (Page $item) {
            $expiry = $item->getExpiryDate();
            $publish = $item->getPublishDate();
            $now = new \DateTime();



            if (!empty($publish) && $publish > $now) {
                return false;
            }

            if (!empty($expiry) && $expiry <= $now) {
                return false;
            }

            return true;
        });

        if (empty($rtn)) {
            $rtn = [];
        }

        return $rtn;
    }
}
