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
            ->setDefaultTemplate('WhiteOctoberAdminBundle::default/edit.html.twig')
            ->setDependences(array(
                'edit' => array(),
            ))
        ;
    }

    public function executeController()
    {
        $findDataById = $this->getActionsVars()->get('findDataById');
        $data = $findDataById($this->container->get('request')->attributes->get('id'));
        if (!$data) {
            throw new NotFoundHttpException();
        }

        $form = $this->buildFormFromFields();
        $form->setData($data);

        $form->bindRequest($this->container->get('request'));
        if ($form->isValid()) {
            $saveData = $this->getActionsVars()->get('saveData');
            $saveData($data);

            return new RedirectResponse($this->generateUrl('list'));
        }

        return $this->render($this->getTemplate(), array('data' => $data, 'form' => $form->createView()));
    }
}
