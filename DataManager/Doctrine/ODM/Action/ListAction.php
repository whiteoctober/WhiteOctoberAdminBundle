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

class ListAction extends BaseListAction
{
    private $queryBuilder;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine.odm.list')
        ;
    }

    public function configureActionsVars(ParameterBag $actionsVars)
    {
        parent::configureActionsVars($actionsVars);

        $dataClass = $this->getDataClass();
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $actionsVars->set('createData', function () use ($dataClass) {
            return new $dataClass();
        });
        $actionsVars->set('findDataById', function ($id) use ($dm, $dataClass) {
            return $dm->getRepository($dataClass)->find($id);
        });
        $actionsVars->set('saveData', function ($data) use ($dm) {
            $dm->persist($data);
            $dm->flush();
        });
        $actionsVars->set('deleteData', function ($data) use ($dm) {
            $dm->remove($data);
            $dm->flush();
        });
    }

    protected function initQuery()
    {
        $this->queryBuilder = $this->get('doctrine.odm.mongodb.document_manager')->createQueryBuilder($this->getDataClass());
    }

    protected function applySimpleFilter($filter)
    {
        foreach ($this->getSimpleFilterFields() as $field) {
            $this->queryBuilder->field($field)->equals(new \MongoRegex(sprintf('/%s/i', $filter)));
        }
    }

    protected function applyAdvancedFilter(array $filters, array $data)
    {
    }

    protected function applySort($sort, $order)
    {
        $this->queryBuilder->sort($sort, $order);
    }

    protected function createPagerfantaAdapter()
    {
        return new DoctrineODMMongoDBAdapter($this->queryBuilder);
    }
}
