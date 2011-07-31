<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Manager\Mandango\Admin;

use WhiteOctober\AdminBundle\Admin\Admin;
use WhiteOctober\AdminBundle\Manager\Mandango\Filter\BooleanFilter;
use WhiteOctober\AdminBundle\Manager\Mandango\Filter\DateFilter;
use WhiteOctober\AdminBundle\Manager\Mandango\Filter\DateTimeFilter;
use WhiteOctober\AdminBundle\Manager\Mandango\Filter\NumberFilter;
use WhiteOctober\AdminBundle\Manager\Mandango\Filter\StringFilter;
use WhiteOctober\AdminBundle\Manager\Mandango\Filter\TimeFilter;
use WhiteOctober\AdminBundle\Manager\Mandango\Action\CRUDActionCollection;

/**
 * MandangoAdmin.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class MandangoAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function preConfigure()
    {
        parent::preConfigure();

        $module = $this;

        $this->addOptions(array(
            'createQueryClosure' => function () use ($module) {
                $query = $module->getContainer()->get('mandango')->getRepository($module->getDataClass())->createQuery();
                foreach ($module->getOption('filterQueryCallbacks') as $callback) {
                    call_user_func($callback, $query, $module);
                }

                return $query;
            },
            'createDataClosure' => function () use ($module) {
                $data = $module->getContainer()->get('mandango')->create($module->getDataClass());
                foreach ($module->getOption('createDataCallbacks') as $callback) {
                    call_user_func($callback, $data, $module);
                }

                return $data;
            },
            'saveDataClosure' => function ($data) use ($module) {
                $data->save();
            },
            'findDataByIdClosure' => function () use ($module) {
                $id = $module->getContainer()->get('request')->attributes->get('id');

                return $module->getOption('createQueryClosure')->__invoke()->criteria(array('_id' => new \MongoId($id)))->one();
            },
            'deleteDataClosure' => function ($data) use ($module) {
                $data->delete();
            },
        ));

        $this->addFieldGuesser('mandango');
    }

    /**
     * {@inheritdoc}
     */
    protected function getCRUDActionCollection()
    {
        return new CRUDActionCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function transformFilter($filter)
    {
        if ('boolean' == $filter) {
            return new BooleanFilter();
        }
        if ('date' == $filter) {
            return new DateFilter();
        }
        if ('date_time' == $filter) {
            return new DateTimeFilter();
        }
        if (in_array($filter, array('float', 'integer'))) {
            return new NumberFilter();
        }
        if ('string' == $filter) {
            return new StringFilter();
        }
        if ('time' == $filter) {
            return new TimeFilter();
        }

        parent::transformFilter($filter);
    }
}
