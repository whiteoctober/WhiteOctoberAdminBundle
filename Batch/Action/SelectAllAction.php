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
use Symfony\Component\HttpFoundation\Response;

class SelectAllAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('batch_select_all')
            ->setRoute('batch_select_all', '/batch/select-all', array(), array('_method' => 'POST'))
            ->setActionDependences(array(
                'batch' => array(),
            ))
        ;
    }

    public function executeController()
    {
        $this->getActionsVars()->get('batchSelector')->selectAll();

        return new Response('OK');
    }
}
