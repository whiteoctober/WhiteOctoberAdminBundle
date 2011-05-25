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

abstract class EditAction extends Action
{
    protected function configure()
    {
        $this
            ->setRoute('edit', '/{id}', array(), array('_method' => 'GET'))
            ->addOptions(array(
                'template' => 'WhiteOctoberAdminBundle::default/edit.html.twig',
            ))
            ->setActionDependences(array(
                'list' => array(
                    'dataActions' => array(
                        'edit'   => array('routeName' => '@edit', 'label' => 'Edit'),
                    ),
                ),
            ))
        ;
    }

    public function executeController()
    {
        $findDataByIdClosure = $this->getActionsVars()->get('findDataByIdClosure');
        $data = $findDataByIdClosure($this->container->get('request')->attributes->get('id'));
        if (!$data) {
            throw new NotFoundHttpException();
        }

        $form = $this->createFormFromFields($this->getFields());
        $form->setData($data);

        return $this->render($this->getOption('template'), array('data' => $data, 'form' => $form->createView()));
    }
}
