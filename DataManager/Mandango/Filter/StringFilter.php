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

use WhiteOctober\AdminBundle\Filter\StringFilter as BaseStringFilter;
use Mandango\Query;

class StringFilter extends BaseStringFilter
{
    protected function configure()
    {
        parent::configure();

        $this->setFilterClosure(function ($fieldName, array $data, Query $query) {
            if ('constaints' == $data['type']) {
                $query->mergeCriteria(array($fieldName => new \MongoRegex(sprintf('/%s/', $data['value']))));
            }
            if ('not_containts' == $data['type']) {
                $query->mergeCriteria(array($fieldName => array('$not' => new \MongoRegex(sprintf('/%s/', $data['value'])))));
            }
            if ('exactly' == $data['type']) {
                $query->mergeCriteria(array($fieldName => $data['value']));
            }
        });
    }
}
