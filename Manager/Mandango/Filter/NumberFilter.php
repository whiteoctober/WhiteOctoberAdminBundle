<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Whiteoctober\AdminBundle\Manager\Mandango\Filter;

use WhiteOctober\AdminBundle\Filter\NumberFilter as BaseNumberFilter;
use Mandango\Query;

class NumberFilter extends BaseNumberFilter
{
    protected function configure()
    {
        parent::configure();

        $this->setFilterClosure(function ($fieldName, array $data, Query $query) {
            if ('exactly' == $data['type']) {
                $query->mergeCriteria(array($fieldName => $data['value']));
            } elseif ('greater_than' == $data['type']) {
                $query->mergeCriteria(array($fieldName => array('$gt' => $data['value'])));
            } elseif ('greater_than_or_equal_to' == $data['type']) {
                $query->mergeCriteria(array($fieldName => array('$gte' => $data['value'])));
            } elseif ('less_than' == $data['type']) {
                $query->mergeCriteria(array($fieldName => array('$lt' => $data['value'])));
            } elseif ('less_than_or_equal_to' == $data['type']) {
                $query->mergeCriteria(array($fieldName => array('$lte' => $data['value'])));
            }
        });
    }
}
