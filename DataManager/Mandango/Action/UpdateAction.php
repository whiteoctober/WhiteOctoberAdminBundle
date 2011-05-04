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

class UpdateAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('mandango.update')
            ->setRoute('update', '/{id}', array(), array('_method' => 'PUT'))
            ->setDefaultTemplate('WhiteOctoberAdminBundle::default/edit.html.twig')
            ->setDependences(array(
                'mandango.edit' => array(),
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

        $form = $this->buildFormFromFields();
        $form->setData($data);

        $form->bindRequest($this->container->get('request'));
        if ($form->isValid()) {
            $data->save();

            $this->get('session')->setFlash('notice','Your object has been saved!');

            return new RedirectResponse($this->generateUrl('list'));
        }

        return $this->render($this->getTemplate(), array('data' => $data, 'form' => $form->createView()));
    }
}
