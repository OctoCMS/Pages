<?php

namespace Octo\Pages\Event;

use b8\Http\Router;
use Octo\Event\Listener;
use Octo\Event\Manager;
use Octo\Store;
use Octo\Template;
use Octo\Pages\Model\Page;

class TemplateFunctions extends Listener
{
    public function registerListeners(Manager $manager)
    {
        $manager->registerListener('TemplateInit', function (array &$functions) {
            $functions['getPages'] = function () {
                return call_user_func_array([$this, 'getPages'], func_get_args());
            };
        });
    }

    protected function getPages($parentId, $limit = 15, $random = false)
    {
        if (empty($parentId)) {
            return [];
        }
        
        $query = Store::get('Page')
            ->where('parent_id', $parentId)
            ->rawWhere('(publish_date IS NULL OR publish_date <= NOW())')
            ->rawWhere('(expiry_date IS NULL OR expiry_date > NOW())')
            ->limit((int)$limit);

        $page = isset($_REQUEST['p']) ? (int)$_REQUEST['p'] : 0;

        if (!empty($page)) {
            $query->offset($page * $limit);
        }

        if ($random) {
            $query->order('RAND()', '');
        } else {
            $query->order('position', 'ASC')->order('publish_date', 'DESC');
        }

        return $query->get();
    }
}
