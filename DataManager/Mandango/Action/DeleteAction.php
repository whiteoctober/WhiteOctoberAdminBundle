<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Mandango\Action;

use WhiteOctober\AdminBundle\Action\Action;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DeleteAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('mandango.delete')
            ->setRoute('delete', '/{id}', array(), array('_method' => 'DELETE'))
            ->setDependences(array(
                'mandango.list' => array(
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
        $data = $dataClass::getRepository()->findOneById($this->container->get('request')->attributes->get('id'));
        if (!$data) {
            throw new NotFoundHttpException();
        }

        $data->delete();

        return new RedirectResponse($this->generateUrl('list'));
    }
}
