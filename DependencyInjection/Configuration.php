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

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * WhiteOctoberAdminExtension configuration structure.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return Symfony\Component\DependencyInjection\Configuration\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('white_october_simple_admin');

        $this->addDataManagersSection($rootNode);
        $this->addAdminsSection($rootNode);

        return $treeBuilder->buildTree();
    }

    private function addDataManagersSection($rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('data_managers')
                    ->children()
                        ->arrayNode('doctrine')
                            ->children()
                                ->booleanNode('orm')->end()
                                ->booleanNode('odm')->end()
                            ->end()
                        ->end()
                        ->booleanNode('mandango')->end()
                        ->booleanNode('propel')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addAdminsSection($rootNode)
    {
        $rootNode
            ->fixXmlConfig('admin')
            ->children()
                ->arrayNode('admins')
                    ->prototype('array')
                        ->performNoDeepMerging()
                        ->children()
                            ->scalarNode('class')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
