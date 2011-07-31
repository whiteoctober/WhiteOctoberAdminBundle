<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Manager\Doctrine\ODM\MongoDB\Action;

use WhiteOctober\AdminBundle\Action\ListAction as BaseListAction;
use Pagerfanta\Adapter\DoctrineODMMongoDBAdapter;

/**
 * ListAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ListAction extends BaseListAction
{
    protected function createQuery()
    {
        return $this->get('doctrine.odm.mongodb.document_manager')->createQueryBuilder($this->getModule()->getDataClass());
    }

    protected function applySimpleFilter($query, array $fields, $filter)
    {
        foreach ($fields as $field) {
            $query->field($field)->equals(new \MongoRegex(sprintf('/%s/i', $filter)));
        }
    }

    protected function applyAdvancedFilter($query, array $filters, array $data)
    {
    }

    protected function applySort($query, $sort, $order)
    {
        $query->sort($sort, $order);
    }

    protected function createPagerfantaAdapter($query)
    {
        return new DoctrineODMMongoDBAdapter($query);
    }
}
