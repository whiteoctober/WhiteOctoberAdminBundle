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
 * EditAction for Propel.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class EditAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('propel.edit')
            ->setRoute('edit', '/{id}', array(), array('_method' => 'GET'))
            ->setDefaultTemplate('WhiteOctoberAdminBundle::default/edit.html.twig')
            ->setDependences(array(
                'propel.list' => array(
                    'data_actions' => array(
                        'edit'   => array('route' => 'edit', 'label' => 'Edit'),
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

        $form = $this->buildFormFromFields();
        $form->setData($data);

        return $this->render($this->getTemplate(), array('data' => $data, 'form' => $form->createView()));
    }
}
