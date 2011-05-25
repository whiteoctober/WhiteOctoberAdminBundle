<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Base\Action;

use WhiteOctober\AdminBundle\Action\Action;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class DeleteAction extends Action
{
    protected function configure()
    {
        $this
            ->setRoute('delete', '/{id}', array(), array('_method' => 'DELETE'))
            ->setActionDependences(array(
                'list' => array(
                    'dataActions' => array(
                        'delete' => array('routeName' => '@delete', 'confirm' => 'Are you sure?', '_method' => 'DELETE', 'label' => 'Delete'),
                    ),
                ),
            ))
        ;
    }

    public function executeController()
    {
        $findDataByIdClosure = $this->getActionsVars()->get('findDataByIdClosure');
        $data = $findDataByIdClosure($this->get('request')->attributes->get('id'));
        if (!$data) {
            throw new NotFoundHttpException();
        }

        $deleteDataClosure = $this->getActionsVars()->get('deleteDataClosure');
        $deleteDataClosure($data);

        return new RedirectResponse($this->generateAdminUrl('list'));
    }
}
