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
 * NewAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class NewAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('white_october_admin.new')
            ->setRoute('new', '/new', array(), array('_method' => 'GET'))
            ->addOptions(array(
                'template' => 'WhiteOctoberAdminBundle::actions/new.html.twig',
            ))
            ->setActionProcessor('list', function ($action) {
                $actions = $action->getOption('actions');
                $actions[] = 'WhiteOctoberAdminBundle::listActions/new.html.twig';
                $action->setOption('actions', $actions);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function executeController()
    {
        $data = $this->getModuleOption('createDataClosure')->__invoke();
        $form = $this->getModuleOption('createFormClosure')->__invoke($this, $data);

        return $this->render($this->getOption('template'), array('form' => $form->createView()));
    }
}
