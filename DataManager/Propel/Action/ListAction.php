<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Propel\Action;

use WhiteOctober\AdminBundle\DataManager\Base\Action\ListAction as BaseListAction;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Pagerfanta\Adapter\PropelAdapter;

/**
 * ListAction for Propel.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class ListAction extends BaseListAction
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('propel.list')

            ->setOption('filterQueryClosure', function (\ModelCriteria $query, array $filterQueryCallbacks, $action, $container) {
                foreach ($filterQueryCallbacks as $callback) {
                    call_user_func($callback, $query, $container);
                }
            })

            ->setOption('createDataClosure', function (array $createDataCallbacks, $action, $container) {
                $dataClass = $action->getDataClass();

                $data = new $dataClass();
                 foreach ($createDataCallbacks as $callback) {
                    call_user_func($callback, $data, $container);
                }

                return $data;
            })

            ->setOption('findDataByIdClosure', function ($id, array $findDataByIdCallbacks, $action, $container) {
                $queryClass = $action->getDataClass().'Query';

                $data = $queryClass::create()->findPk($id);
                foreach ($findDataByIdCallbacks as $callback) {
                    if ($data) {
                        $data = call_user_func($callback, $data, $container);
                    }
                }

                return $data;
            })

            ->setOption('saveDataClosure', function ($data, $action, $container) {
                $data->save();
            })

            ->setOption('deleteDataClosure', function ($data, $action, $container) {
                $data->delete();
            })
        ;
    }

    /**
     * List
     */
    protected function createQuery()
    {
        $dataClass = $this->getDataClass();
        $queryClass = $dataClass.'Query';

        return $queryClass::create();
    }

    protected function applySimpleFilter($query, $filter)
    {
        throw new \RuntimeException('Not yet implemented !');
    }

    protected function applyAdvancedFilter($query, array $filters, array $data)
    {
        foreach ($filters as $fieldName => $filter) {
            if (isset($data[$fieldName]) && null !== $data[$fieldName]) {
                $filter->filter($fieldName, $data[$fieldName], $query);
            }
        }
    }

    protected function transformAdvancedFilterType($type)
    {
        throw new \RuntimeException('Not yet implemented !');
    }

    protected function applySort($query, $sort, $order)
    {
        $query->orderBy($sort, $order);
    }

    protected function createPagerfantaAdapter($query)
    {
        return new PropelAdapter($query);
    }
}
