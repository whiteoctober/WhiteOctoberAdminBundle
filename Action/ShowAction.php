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
 * ShowAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ShowAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('white_october_admin.show')
            ->setRoute('show', '/{id}/show', array(), array('_method' => 'GET'))
            ->addOptions(array(
                'template' => 'WhiteOctoberAdminBundle::actions/show.html.twig',
            ))
            ->setActionProcessor('list', function ($action) {
                $dataActions = $action->getOption('dataActions');
                $dataActions[] = 'WhiteOctoberAdminBundle::dataActions/show.html.twig';
                $action->setOption('dataActions', $dataActions);
            })
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

        return $this->render($this->getOption('template'), array('data' => $data));
    }
}
