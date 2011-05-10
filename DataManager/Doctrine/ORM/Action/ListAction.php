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

class ListAction extends BaseListAction
{
    private $queryBuilder;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine.orm.list')
        ;
    }

    public function configureActionsVars(ParameterBag $actionsVars)
    {
        parent::configureActionsVars($actionsVars);

        $dataClass = $this->getDataClass();
        $em = $this->get('doctrine.orm.entity_manager');

        $actionsVars->set('createData', function () use ($dataClass) {
            return new $dataClass();
        });
        $actionsVars->set('findDataById', function ($id) use ($em, $dataClass) {
            return $em->getRepository($dataClass)->find($id);
        });
        $actionsVars->set('saveData', function ($data) use ($em) {
            $em->persist($data);
            $em->flush();
        });
        $actionsVars->set('deleteData', function ($data) use ($em) {
            $em->remove($data);
            $em->flush();
        });
    }

    protected function initQuery()
    {
        $queryBuilder = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from($this->getDataClass(), 'u')
        ;

        $this->queryBuilder = $queryBuilder;
    }

    protected function applySimpleFilter($filter)
    {
        foreach ($this->getSimpleFilterFields() as $field) {
            $this->queryBuilder->orWhere($this->queryBuilder->expr()->like('u.'.$field, ':filter'));
        }

        $this->queryBuilder->setParameter('filter', '%'.$filter.'%');
    }

    protected function applySort($sort, $order)
    {
        $this->queryBuilder->add('orderBy', sprintf('u.%s %s', $sort, $order));
    }

    protected function createPagerfantaAdapter()
    {
        return new DoctrineORMAdapter($this->queryBuilder);
    }
}
