<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Doctrine\ODM\Action;

use WhiteOctober\AdminBundle\Action\Action;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CreateAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('doctrine.odm.create')
            ->setRoute('create', '/', array(), array('_method' => 'POST'))
            ->setDefaultTemplate('WhiteOctoberAdminBundle::default/new.html.twig')
            ->setDependences(array(
                'doctrine.odm.new' => array(),
            ))
        ;
    }

    public function executeController()
    {
        $dataClass = $this->getDataClass();
        $data = new $dataClass();

        $form = $this->buildFormFromFields();
        $form->setData($data);

        $form->bindRequest($this->container->get('request'));
        if ($form->isValid()) {
            $em = $this->get('doctrine.odm.mongodb.document_manager');
            $em->persist($data);
            $em->flush();

            $this->get('session')->setFlash('notice','Your object has been saved!');

            return new RedirectResponse($this->generateUrl('list'));
        }

        return $this->render($this->getTemplate(), array('form' => $form->createView()));
    }
}
