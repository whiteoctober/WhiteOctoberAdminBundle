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
                    if (!is_array($datas)) $datas = array($datas);
                    $objects = \PropelQuery::from($action->getDataClass())->findPks($datas);
                    foreach ($objects as $object) {
                        $object->delete();
                    }
                };
                return $actions;
            })
        ;
    }
}
