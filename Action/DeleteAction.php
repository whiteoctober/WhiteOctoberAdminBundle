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
 * DeleteAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DeleteAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('white_october_admin.delete')
            ->setRoute('delete', '/{id}', array(), array('_method' => 'DELETE'))
            ->setActionProcessor('list', function ($action) {
                $dataActions = $action->getOption('dataActions');
                $dataActions[] = 'WhiteOctoberAdminBundle::dataActions/delete.html.twig';
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

        $this->getModuleOption('deleteDataClosure')->__invoke($data);

        return $this->redirect($this->generateModuleUrl('list'));
    }
}
