<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Whiteoctober\AdminBundle\DataManager\Mandango\Filter;

use WhiteOctober\AdminBundle\Filter\BooleanFilter as BaseBooleanFilter;
use Mandango\Query;

class BooleanFilter extends BaseBooleanFilter
{
    protected function configure()
    {
        parent::configure();

        $this->setFilterClosure(function ($fieldName, array $data, Query $query) {
            if ('yes_or_no' == $data['value']) {
                return;
            }
            if ('yes' == $data['value']) {
                $query->mergeCriteria(array($fieldName => true));
            }
            if ('no' == $data['value']) {
                $query->mergeCriteria(array($fieldName => false));
            }
        });
    }
}
