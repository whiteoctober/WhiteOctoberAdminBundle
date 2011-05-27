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

            ->addOption('entityManagerName', null)

            ->setOption('filterQueryClosure', function (QueryBuilder $query, array $filterQueryCallbacks, $action, $container) {
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
                $em = $container->get('doctrine')->getEntityManager($action->getOption('entityManagerName'));

                $data = $em->getRepository($action->getDataClass())->find($id);
                foreach ($findDataByIdCallbacks as $callback) {
                    if ($data) {
                        $data = call_user_func($callback, $data, $em, $container);
                    }
                }

                return $data;
            })

            ->setOption('saveDataClosure', function ($data, $action, $container) {
                $em = $container->get('doctrine')->getEntityManager($action->getOption('entityManagerName'));

                $em->persist($data);
                $em->flush();
            })

            ->setOption('deleteDataClosure', function ($data, $action, $container) {
                $em = $container->get('doctrine')->getEntityManager($action->getOption('entityManagerName'));

                $em->remove($data);
                $em->flush();
            })
        ;
    }

    /*
     * List
     */
    protected function createQuery()
    {
        $em = $this->container->get('doctrine')->getEntityManager($this->getOption('entityManagerName'));

        $queryBuilder = $em->createQueryBuilder();
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
        return new DoctrineORMAdapter($query->getQuery());
    }
}
