<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Manager\Doctrine\ODM\MongoDB\Admin;

use WhiteOctober\AdminBundle\Admin\Admin;
use WhiteOctober\AdminBundle\Manager\Doctrine\ODM\MongoDB\Action\CRUDActionCollection;

/**
 * DoctrineODMAdmin.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class DoctrineODMMongoDBAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected function preConfigure()
    {
        parent::preConfigure();

        $module = $this;

        $this->addOptions(array(
            'createDataClosure' => function () use ($module) {
                $dataClass = $module->getDataClass();
                return new $dataClass;
            },
            'saveDataClosure' => function ($data) use ($module) {
                $dm = $module->getContainer()->get('doctrine.odm.mongodb.document_manager');
                $dm->persist($data);
                $dm->flush();
            },
            'findDataByIdClosure' => function () use ($module) {
                $dm = $module->getContainer()->get('doctrine.odm.mongodb.document_manager');
                $id = $module->getContainer()->get('request')->attributes->get('id');

                return $dm->getRepository($module->getDataClass())->find($id);
            },
            'deleteDataClosure' => function ($data) use ($module) {
                $dm = $module->getContainer()->get('doctrine.odm.mongodb.document_manager');
                $dm->remove($data);
                $dm->flush();
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getCRUDActionCollection()
    {
        return new CRUDActionCollection();
    }
}
