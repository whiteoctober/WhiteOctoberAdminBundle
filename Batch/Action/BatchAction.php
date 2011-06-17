<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Batch\Action;

use WhiteOctober\AdminBundle\Action\Action;
use WhiteOctober\AdminBundle\Batch\BatchSelector;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BatchAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('batch')
            ->setRoute('batch', '/batch/action', array(), array('_method' => 'POST'))
            ->addOptions(array(
                'actions'        => array(),
                'checkIdClosure' => function ($id) {
                    return true;
                },
            ))
            ->setActionDependences(array(
                'list' => array(),
            ))
        ;
    }

    public function configureActionsVars(ParameterBag $actionsVars)
    {
        if ($this->container->isScopeActive('request')) {
            $actionsVars->set('batchSelector', new BatchSelector($actionsVars->get('admin_session')));
        }

        $checkIdClosure = $this->getOption('checkIdClosure');
        if (!$checkIdClosure instanceof \Closure) {
            throw new \RuntimeException('The checkIdClosure is not a closure.');
        }
        $container = $this->container;
        $actionsVars->set('batchCheckIdClosure', function ($id) use ($checkIdClosure, $container) {
            return call_user_func_array($checkIdClosure, array($id, $container));
        });
    }

    public function executeController()
    {
        $actions = $this->getOption('actions');
        $actionName = $this->get('request')->request->get('action');
        if (!isset($actions[$actionName])) {
            throw new NotFoundHttpException();
        }

        $action = $actions[$actionName];
        $selected = $this->getActionsVars()->get('batchSelector')->getSelected();
        $action($selected, $this->container, $this);

        return new RedirectResponse($this->generateAdminUrl('list'));
    }
}
