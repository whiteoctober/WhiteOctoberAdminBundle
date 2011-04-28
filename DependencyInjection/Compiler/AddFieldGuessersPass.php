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

class AddFieldGuessersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('white_october_admin.admin_factory')) {
            return;
        }

        $fieldGuesserIds = array();
        foreach ($container->findTaggedServiceIds('white_october_admin.field_guesser') as $serviceId => $arguments) {
            $alias = isset($arguments[0]['alias']) ? $arguments[0]['alias'] : $serviceId;

            $fieldGuesserIds[$alias] = $serviceId;
        }

        $container->getDefinition('white_october_admin.admin_factory')->replaceArgument(1, $fieldGuesserIds);
    }
}
