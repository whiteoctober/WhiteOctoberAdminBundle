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

    protected function initQuery()
    {
        $this->queryBuilder = $this->get('doctrine.odm.mongodb.document_manager')->createQueryBuilder($this->getDataClass());
    }

    protected function applyFilter($filter)
    {
        foreach ($this->getFilterFields() as $field) {
            $this->queryBuilder->field($field)->equals(new \MongoRegex(sprintf('/%s/i', $filter)));
        }
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
