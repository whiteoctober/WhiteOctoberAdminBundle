<?php


/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Propel\Admin;

use WhiteOctober\AdminBundle\Admin\Admin;
use WhiteOctober\AdminBundle\Batch\Action\BatchActionCollection;
use WhiteOctober\AdminBundle\Batch\BatchSelector;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * PropelAdmin class
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
abstract class PropelAdmin extends Admin
{
    protected function preConfigure()
    {
        parent::preConfigure();

        $this
            ->addParameterToPropagate('hash')
            ->addFieldGuesser('propel')
            ->addAction('propel.crud')
            ->addAction(new BatchActionCollection())
            ->addActionOptionProcessor('list', 'batch_actions', function (array $actions) {
                $actions['delete'] = 'Delete';
                return $actions;
            })
            ->addActionOptionProcessor('batch', 'actions', function (array $actions) {
                $actions['delete'] = function ($datas, ContainerInterface $container, $action) {
                    $queryClass = \PropelQuery::from($action->getDataClass());
                    if (BatchSelector::ALL !== $datas) {
                        $queryClass
                            ->filterByPrimaryKeys($datas)
                            ->delete();
                    } else {
                        $queryClass->deleteAll();
                    }
                };
                return $actions;
            })
        ;
    }
}

