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

abstract class CreateAction extends Action
{
    protected function configure()
    {
        $this
            ->setRoute('create', '/', array(), array('_method' => 'POST'))
            ->setDefaultTemplate('WhiteOctoberAdminBundle::default/new.html.twig')
            ->setDependences(array(
                'new' => array(),
            ))
        ;
    }

    public function executeController()
    {
        $createData = $this->getActionsVars()->get('createData');
        $data = $createData();

        $form = $this->buildFormFromFields();
        $form->setData($data);

        $form->bindRequest($this->container->get('request'));
        if ($form->isValid()) {
            $saveData = $this->getActionsVars()->get('saveData');
            $saveData($data);

            return new RedirectResponse($this->generateUrl('list'));
        }

        return $this->render($this->getTemplate(), array('form' => $form->createView()));
    }
}
