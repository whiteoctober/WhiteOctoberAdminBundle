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

abstract class UpdateAction extends Action
{
    protected function configure()
    {
        $this
            ->setRoute('update', '/{id}', array(), array('_method' => 'PUT'))
            ->addOptions(array(
                'template' => 'WhiteOctoberAdminBundle::default/edit.html.twig',
            ))
            ->setActionDependences(array(
                'edit' => array(),
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

        $form->bindRequest($this->container->get('request'));
        if ($form->isValid()) {
            $saveDataClosure = $this->getActionsVars()->get('saveDataClosure');
            $saveDataClosure($data);

            return new RedirectResponse($this->generateAdminUrl('list'));
        }

        return $this->render($this->getOption('template'), array('data' => $data, 'form' => $form->createView()));
    }
}
