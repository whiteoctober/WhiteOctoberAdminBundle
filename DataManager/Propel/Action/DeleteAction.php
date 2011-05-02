<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Propel\Action;

use WhiteOctober\AdminBundle\Action\Action;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * DeleteAction for Propel.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class DeleteAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('propel.delete')
            ->setRoute('delete', '/{id}', array(), array('_method' => 'DELETE'))
            ->setDependences(array(
                'propel.list' => array(
                    'data_actions' => array(
                        'delete'   => array('route' => 'delete', 'confirm' => 'Are you sure?', '_method' => 'DELETE', 'label' => 'Delete'),
                    ),
                ),
            ))
        ;
    }

    public function executeController()
    {
        $dataClass = $this->getDataClass();
        $queryClass = $dataClass.'Query';

        $data = $queryClass::create()->findPk($this->container->get('request')->attributes->get('id'));
        if (!$data) {
            throw new NotFoundHttpException();
        }

        $data->delete();

        return new RedirectResponse($this->generateUrl('list'));
    }
}
