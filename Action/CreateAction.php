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
 * CreateAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class CreateAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('white_october_admin.create')
            ->setRoute('create', '', array(), array('_method' => 'POST'))
            ->addOptions(array(
                'template' => 'WhiteOctoberAdminBundle::actions/new.html.twig',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function executeController()
    {
        $data = $this->getModuleOption('createDataClosure')->__invoke();
        $form = $this->getModuleOption('createFormClosure')->__invoke($this, $data);

        $form->bindRequest($this->container->get('request'));
        if ($form->isValid()) {
            $this->getModuleOption('saveDataClosure')->__invoke($data);

            return $this->redirect($this->generateModuleUrl('list'));
        }

        return $this->render($this->getOption('template'), array('form' => $form->createView()));
    }
}
