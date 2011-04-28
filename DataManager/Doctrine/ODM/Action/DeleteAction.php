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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DeleteAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('doctrine.odm.delete')
            ->setRoute('delete', '/{id}', array(), array('_method' => 'DELETE'))
            ->setDependences(array(
                'doctrine.odm.list' => array(
                    'data_actions' => array(
                        'delete'   => array('route' => 'delete', '_method' => 'DELETE', 'label' => 'Delete'),
                    ),
                ),
            ))
        ;
    }

    public function executeController()
    {
        $dataClass = $this->getDataClass();
        $em = $this->get('doctrine.odm.mongodb.document_manager');
        $data = $em->getRepository($dataClass)->find($this->container->get('request')->attributes->get('id'));
        if (!$data) {
            throw new NotFoundHttpException();
        }

        $em->remove($data);
        $em->flush();

        return new RedirectResponse($this->generateUrl('list'));
    }
}
