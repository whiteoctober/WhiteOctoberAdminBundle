<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Manager\Mandango\Action;

use WhiteOctober\AdminBundle\Action\ListAction as BaseListAction;
use Pagerfanta\Adapter\MandangoAdapter;

/**
 * ListAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ListAction extends BaseListAction
{
    protected function applySimpleFilter($query, array $fields, $filter)
    {
        foreach ($fields as $field) {
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

    protected function applySort($query, $sort, $order)
    {
        $query->sort(array($sort => 'asc' == $order ? \MongoCollection::ASCENDING : \MongoCollection::DESCENDING));
    }

    protected function createPagerfantaAdapter($query)
    {
        return new MandangoAdapter($query);
    }
}
