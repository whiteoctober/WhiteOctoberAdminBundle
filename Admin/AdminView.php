<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Admin;

use Symfony\Component\DependencyInjection\ContainerAware;

class AdminView extends ContainerAware
{
    private $admin;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    public function getBaseTemplate()
    {
        return $this->admin->getBaseTemplate();
    }

    public function path($name, array $parameters = array())
    {
        return $this->admin->generateUrl($name, $parameters, false);
    }

    public function url($name, array $parameters = array())
    {
        return $this->admin->generateUrl($name, $parameters, true);
    }

    public function getName()
    {
        return $this->admin->getName();
    }

    public function getDataClass()
    {
        return $this->admin->getDataClass();
    }

    public function getDataClassName()
    {
        return $this->admin->getDataClassName();
    }

    public function getParametersToPropagate()
    {
        return $this->admin->getParametersToPropagate();
    }

    public function generateDataAction($data, $routeName, array $routeParameters = array())
    {
        foreach ($routeParameters as $parameter => &$value) {
            if ('@' == $value[0]) {
                $value = $this->admin->getDataFieldValue($data, substr($value, 1));
            }
        }

        if ('@' == $routeName[0]) {
            return $this->admin->generateUrl(substr($routeName, 1), $routeParameters);
        }

        return $this->container->get('router')->generate($routeName, $routeParameters);
    }
}
