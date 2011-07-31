<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Action;

use Pablodip\ModuleBundle\Action\Action;

/**
 * UpdateAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class UpdateAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('white_october_admin.update')
            ->setRoute('update', '/{id}', array(), array('_method' => 'PUT'))
            ->addOptions(array(
                'template' => 'WhiteOctoberAdminBundle::default/edit.html.twig',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function executeController()
    {
        if (!$data = $this->getModuleOption('findDataByIdClosure')->__invoke()) {
            throw $this->createNotFoundException();
        }

        $form = $this->getModuleOption('createFormClosure')->__invoke($this, $data);

        $form->bindRequest($this->container->get('request'));
        if ($form->isValid()) {
            $this->getModuleOption('saveDataClosure')->__invoke($data);

            return $this->redirect($this->generateModuleUrl('list'));
        }

        return $this->render($this->getOption('template'), array('data' => $data, 'form' => $form->createView()));
    }
}
