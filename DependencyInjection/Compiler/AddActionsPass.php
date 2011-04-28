<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AddActionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('white_october_admin.action_factory')) {
            return;
        }

        $actions = array();
        foreach ($container->findTaggedServiceIds('white_october_admin.action') as $serviceId => $arguments) {
            $alias = isset($arguments[0]['alias']) ? $arguments[0]['alias'] : $serviceId;

            $actions[$alias] = new Reference($serviceId);
        }

        $container->getDefinition('white_october_admin.action_factory')->addMethodCall('add', array($actions));
    }
}
