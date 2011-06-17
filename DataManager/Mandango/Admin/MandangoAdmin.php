<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Mandango\Admin;

use WhiteOctober\AdminBundle\Admin\Admin;
use WhiteOctober\AdminBundle\Batch\Action\BatchActionCollection;
use WhiteOctober\AdminBundle\Batch\BatchSelector;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class MandangoAdmin extends Admin
{
    protected function preConfigure()
    {
        parent::preConfigure();

        $this
            ->addParameterToPropagate('hash')
            ->addFieldGuesser('mandango')
            ->addAction('mandango.crud')

            // batch
            ->addAction(new BatchActionCollection())
            ->addActionOptionProcessor('batch', 'actions', function (array $actions) {
                $actions['delete'] = function ($datas, ContainerInterface $container, $action) {
                    $query = array();
                    if (BatchSelector::ALL !== $datas) {
                        foreach ($datas as &$data) {
                            $data = new \MongoId($data);
                        }
                        $query['_id'] = array('$in' => $datas);
                    }

                    $container->get('mandango')->getRepository($action->getDataClass())->remove($query);
                };

                return $actions;
            })
            ->addActionOptionProcessor('list', 'batch_actions', function (array $actions) {
                $actions['delete'] = 'Delete';

                return $actions;
            })
        ;
    }
}
