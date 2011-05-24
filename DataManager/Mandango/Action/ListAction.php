<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Mandango\Action;

use WhiteOctober\AdminBundle\DataManager\Base\Action\ListAction as BaseListAction;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Pagerfanta\Adapter\MandangoAdapter;
use Mandango\Query;

class ListAction extends BaseListAction
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('mandango.list')

            ->setOption('filterQueryClosure', function (Query $query, array $filterQueryCallbacks, $action, $container) {
                foreach ($filterQueryCallbacks as $callback) {
                    call_user_func($callback, $query, $container);
                }
            })

            ->setOption('createDataClosure', function (array $createDataCallbacks, $action, $container) {
                $data = $container->get('mandango')->create($action->getDataClass());
                foreach ($createDataCallbacks as $callback) {
                    call_user_func($callback, $data, $container);
                }

                return $data;
            })

            ->setOption('findDataByIdClosure', function ($id, array $findDataByIdCallbacks, $action, $container) {
                $data = $container->get('mandango')->getRepository($action->getDataClass())->findOneById($id);
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

    /*
     * List
     */
    protected function createQuery()
    {
        return $this->container->get('mandango')->getRepository($this->getDataClass())->createQuery();
    }

    protected function applySimpleFilter($query, $filter)
    {
        foreach ($this->getSimpleFilterFields() as $field) {
            $query->mergeCriteria(array($field => new \MongoRegex(sprintf('/%s/', $filter))));
        }
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
        if ('boolean' == $type) {
            return new \WhiteOctober\AdminBundle\DataManager\Mandango\Filter\BooleanFilter();
        }
        if ('string' == $type) {
            return new \WhiteOctober\AdminBundle\DataManager\Mandango\Filter\StringFilter();
        }
    }

    protected function applySort($query, $sort, $order)
    {
        $query->sort(array($sort => 'asc' == $order ? \MongoCollection::ASCENDING : \MongoCollection::DESCENDING));
    }

    protected function createPagerfantaAdapter($query)
    {
        return new MandangoAdapter($query);
    }
}
