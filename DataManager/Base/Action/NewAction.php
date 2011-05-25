<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Base\Action;

use WhiteOctober\AdminBundle\Action\Action;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class NewAction extends Action
{
    protected function configure()
    {
        $this
            ->setRoute('new', '/new', array(), array('_method' => 'GET'))
            ->addOptions(array(
                'template' => 'WhiteOctoberAdminBundle::default/new.html.twig',
            ))
            ->setActionDependences(array(
                'list' => array(
                    'actions' => array(
                        'new' => array('route' => 'new', 'label' => 'New'),
                    ),
                ),
            ))
        ;
    }

    public function executeController()
    {
        $createDataClosure = $this->getActionsVars()->get('createDataClosure');
        $data = $createDataClosure();

        $form = $this->createFormFromFields($this->getFields());
        $form->setData($data);

        return $this->render($this->getOption('template'), array('form' => $form->createView()));
    }
}
