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

use WhiteOctober\AdminBundle\Filter\DateFilter as BaseDateFilter;
use Mandango\Query;

class DateFilter extends BaseDateFilter
{
    protected function configure()
    {
        parent::configure();

        $this->setFilterClosure(function ($fieldName, array $data, Query $query) {
            $fieldQuery = array();
            if ($data['from']) {
                $fieldQuery['$gte'] = new \MongoDate($data['from']->getTimestamp());
            }
            if ($data['to']) {
                $fieldQuery['$lte'] = new \MongoDate($data['to']->getTimestamp());
            }

            $query->mergeCriteria(array($fieldName => $fieldQuery));
        });
    }
}
