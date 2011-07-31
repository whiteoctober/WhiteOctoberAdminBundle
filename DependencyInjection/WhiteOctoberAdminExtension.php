<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * WhiteOctoberAdminExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class WhiteOctoberAdminExtension extends Extension
{
    /**
     * Responds to the "white_october_admin" configuration parameter.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('admin.xml');

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->process($configuration->getConfigTree(), $configs);

        // data managers
        if (!empty($config['managers']['mandango'])) {
            $loader->load('manager_mandango.xml');
        }
        if (!empty($config['managers']['doctrine']['orm'])) {
            $loader->load('manager_doctrine_orm.xml');
        }
        if (!empty($config['managers']['doctrine']['odm'])) {
            $loader->load('manager_doctrine_odm.xml');
        }
        if (!empty($config['managers']['propel'])) {
            $loader->load('manager_propel.xml');
        }
    }
}
