<?php

namespace Octo\Pages\Event;

use b8\Http\Router;
use Octo\Event\Listener;
use Octo\Event\Manager;

class RegisterRoutes extends Listener
{
    public function registerListeners(Manager $manager)
    {
        $manager->registerListener('BackupRoutes', function (Router $router) {
            $router->register('/', ['controller' => 'Page', 'action' => 'View']);
        });

        $manager->registerListener('BackupRoutes', function (Router $router) {
            $router->register('/sitemap.xml', ['controller' => 'Sitemap', 'action' => 'xml']);
        });
    }
}
