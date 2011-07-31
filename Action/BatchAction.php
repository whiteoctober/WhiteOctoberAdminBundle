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
 * BatchAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class BatchAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('white_october_admin.batch')
            ->setRoute('batch', '/{id}', array(), array('_method' => 'POST'))
            ->addOptions(array(
                'actions' => array(),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function executeController()
    {
        $request = $this->get('request');

        $actions = $this->getOption('actions');
        $actionName = $request->request->get('action');
        if (!isset($actions[$actionName])) {
            throw $this->createNotFoundException();
        }
        $action = $actions[$actionName];

        $datas = $request->request->get('all') ? true : $request->request->get('selected', array());

        $action($datas, $this->container);

        return $this->redirect($this->generateModuleUrl('list'));
    }
}
