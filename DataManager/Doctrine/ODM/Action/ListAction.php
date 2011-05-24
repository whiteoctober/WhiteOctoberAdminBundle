<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Doctrine\ODM\Action;

use WhiteOctober\AdminBundle\DataManager\Base\Action\ListAction as BaseListAction;
use Symfony\Component\HttpFoundation\ParameterBag;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;
use Doctrine\ODM\MongoDB\Query\Builder;

class ListAction extends BaseListAction
{
    protected function configure()
    {
        parent::configure();

        $action = $this;

        $this
            ->setName('doctrine.odm.list')

            ->setOption('filterQueryClosure', function (Builder $query, array $filterQueryCallbacks, $action, $container) {
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
                $dm = $container->get('doctrine.odm.mongodb.document_manager');

                $data = $dm->getRepository($action->getDataClass())->find($id);
                foreach ($findDataByIdCallbacks as $callback) {
                    if ($data) {
                        $data = call_user_func($callback, $data, $dm, $container);
                    }
                }

                return $data;
            })

            ->setOption('saveDataClosure', function ($data, $action, $container) {
                $dm = $container->get('doctrine.odm.mongodb.document_manager');

                $dm->persist($data);
                $dm->flush();
            })

            ->setOption('deleteDataClosure', function ($data, $action, $container) {
                $dm = $container->get('doctrine.odm.mongodb.document_manager');

                $dm->remove($data);
                $dm->flush();
            })
        ;
    }

    /*
     * List.
     */
    protected function createQuery()
    {
        return $this->get('doctrine.odm.mongodb.document_manager')->createQueryBuilder($this->getDataClass());
    }

    protected function applySimpleFilter($query, $filter)
    {
        foreach ($this->getSimpleFilterFields() as $field) {
            $queryBuilder->field($field)->equals(new \MongoRegex(sprintf('/%s/i', $filter)));
        }
    }

    protected function applyAdvancedFilter($query, array $filters, array $data)
    {
    }

    protected function applySort($query, $sort, $order)
    {
        $queryBuilder->sort($sort, $order);
    }

    protected function createPagerfantaAdapter($query)
    {
        return new DoctrineODMMongoDBAdapter($query);
    }
}
