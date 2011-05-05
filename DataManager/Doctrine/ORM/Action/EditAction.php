<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Doctrine\ORM\Action;

use WhiteOctober\AdminBundle\Action\Action;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EditAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('doctrine.orm.edit')
            ->setRoute('edit', '/{id}', array(), array('_method' => 'GET'))
            ->setDefaultTemplate('WhiteOctoberAdminBundle::default/edit.html.twig')
            ->setDependences(array(
                'doctrine.orm.list' => array(
                    'data_actions' => array(
                        'edit'   => array('route' => 'edit', '_method' => 'GET','label' => 'Edit'),
                    ),
                ),
            ))
        ;
    }

    public function executeController()
    {
        $dataClass = $this->getDataClass();
        $em = $this->get('doctrine.orm.entity_manager');
        $data = $em->getRepository($dataClass)->find($this->container->get('request')->attributes->get('id'));
        if (!$data) {
            throw new NotFoundHttpException();
        }

        $form = $this->buildFormFromFields();
        $form->setData($data);

        return $this->render($this->getTemplate(), array('data' => $data, 'form' => $form->createView()));
    }
}
