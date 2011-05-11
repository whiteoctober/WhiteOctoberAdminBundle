<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Doctrine\ORM\Action;

use WhiteOctober\AdminBundle\DataManager\Base\Action\ListAction as BaseListAction;
use Symfony\Component\HttpFoundation\ParameterBag;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Doctrine\ORM\QueryBuilder;

class ListAction extends BaseListAction
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine.orm.list')
        ;
    }

    /*
     * General Closures.
     */
    protected function getFilterQueryClosure()
    {
        $container = $this->container;

        return function (QueryBuilder $query, array $filterQueryCallbacks) use ($container) {
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
        $em = $this->get('doctrine.orm.entity_manager');

        return function ($id, array $findDataByIdCallbacks) use ($em, $dataClass, $container) {
            $data = $em->getRepository($dataClass)->find($id);
            foreach ($findDataByIdCallbacks as $callback) {
                if ($data) {
                    $data = call_user_func($callback, $data, $em, $container);
                }
            }

            return $data;
        };
    }

    protected function getSaveDataClosure()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        return function ($data) use ($em) {
            $em->persist($data);
            $em->flush();
        };
    }

    protected function getDeleteDataClosure()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        return function ($data) use ($em) {
            $em->remove($data);
            $em->flush();
        };
    }

    /*
     * List
     */
    protected function createQuery()
    {
        $queryBuilder = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from($this->getDataClass(), 'u')
        ;

        return $queryBuilder;
    }

    protected function applySimpleFilter($query, $filter)
    {
        foreach ($this->getSimpleFilterFields() as $field) {
            $this->queryBuilder->orWhere($this->queryBuilder->expr()->like('u.'.$field, ':filter'));
        }

        $query->setParameter('filter', '%'.$filter.'%');
    }

    protected function applyAdvancedFilter($query, array $filters, array $data)
    {
    }

    protected function applySort($query, $sort, $order)
    {
        $query->add('orderBy', sprintf('u.%s %s', $sort, $order));
    }

    protected function createPagerfantaAdapter($query)
    {
        return new DoctrineORMAdapter($query);
    }
}
