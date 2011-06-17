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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

class SelectAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('batch_select')
            ->setRoute('batch_select', '/batch/select', array(), array('_method' => 'POST'))
            ->setActionDependences(array(
                'batch' => array(),
            ))
        ;
    }

    public function executeController()
    {
        $id = $this->get('request')->request->get('id');
        $checkIdClosure = $this->getActionsVars()->get('batchCheckIdClosure');
        if (!$id || !$checkIdClosure($id)) {
            throw new NotFoundHttpException();
        }

        $this->getActionsVars()->get('batchSelector')->select($id);

        return new Response('OK');
    }
}
