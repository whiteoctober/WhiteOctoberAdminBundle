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

        $this
            ->setName('doctrine.odm.list')
        ;
    }

    /*
     * General Closures.
     */
    protected function getFilterQueryClosure()
    {
        $container = $this->container;

        return function (Builder $query, array $filterQueryCallbacks) use ($container) {
            foreach ($filterQueryCallbacks as $callback) {
                call_user_func($callback, $query, $container);
            }
        };
    }

    protected function getCreateDataClosure()
    {
        $container = $this->container;
        $dataClass = $this->getDataClass();

        return function (array $createDataCallbacks) use ($dataClass, $container) {
            $data = new $dataClass();
            foreach ($createDataCallbacks as $callback) {
                call_user_func($callback, $data, $container);
            }

            return $data;
        };
    }

    protected function getFindDataByIdClosure()
    {
        $container = $this->container;
        $dataClass = $this->getDataClass();
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        return function ($id, array $findDataByIdCallbacks) use ($dm, $dataClass, $container) {
            $data = $dm->getRepository($dataClass)->find($id);
            foreach ($findDataByIdCallbacks as $callback) {
                if ($data) {
                    $data = call_user_func($callback, $data, $dm, $container);
                }
            }

            return $data;
        };
    }

    protected function getSaveDataClosure()
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        return function ($data) use ($dm) {
            $dm->persist($data);
            $dm->flush();
        };
    }

    protected function getDeleteDataClosure()
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        return function ($data) use ($dm) {
            $dm->remove($data);
            $dm->flush();
        };
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
