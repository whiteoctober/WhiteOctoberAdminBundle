<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Routing;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use WhiteOctober\AdminBundle\Admin\AdminFactory;

class AdminFactoryLoader extends FileLoader
{
    private $adminFactory;

    public function __construct(AdminFactory $adminFactory)
    {
        $this->adminFactory = $adminFactory;
    }

    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        foreach ($this->adminFactory->getAdmins() as $adminId => $admin) {
            $routePatternPrefix = $admin->getRoutePatternPrefix();
            $routeNamePrefix = $admin->getRouteNamePrefix();

            foreach ($admin->getActions() as $action) {
                $defaults = array(
                    '_controller' => 'WhiteOctoberAdminBundle:Admin:execute',
                    '_white_october_admin.admin'  => $adminId,
                    '_white_october_admin.action' => $action->getFullName(),
                );
                $defaults = array_merge($action->getRouteDefaults(), $defaults);
                $route = new Route($routePatternPrefix.$action->getRoutePatternSuffix(), $defaults, $action->getRouteRequirements());

                $collection->add($ups = $routeNamePrefix.'_'.$action->getRouteNameSuffix(), $route);

                $reflection = new \ReflectionObject($action);
                $collection->addResource(new FileResource($reflection->getFileName()));
            }

            $reflection = new \ReflectionObject($admin);
            $collection->addResource(new FileResource($reflection->getFileName()));
        }

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return 'white_october_admin' == $type;
    }
}
