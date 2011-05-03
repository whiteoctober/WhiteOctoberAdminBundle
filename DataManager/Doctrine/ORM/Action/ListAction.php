<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Doctrine\ORM\Action;

use WhiteOctober\AdminBundle\Action\Action;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;

class ListAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('doctrine.orm.list')
            ->setRoute('list', '/', array(), array('_method' => 'GET'))
            ->setDefaultTemplate('WhiteOctoberAdminBundle::default/list.html.twig')
            ->addOptions(array(
                'maxPerPage'        => 10,
                'pagerfantaView'    => 'white_october_admin',
                'pagerfantaOptions' => array(),
            ))
        ;
    }

    public function executeController()
    {
        $dataClass = $this->getDataClass();
        $em = $this->get('doctrine.orm.entity_manager');

        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from($dataClass, 'u')
        ;
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($this->getOption('maxPerPage'));
        if ($page = $this->get('request')->query->get('page')) {
            try {
                $pagerfanta->setCurrentPage($page);
            } catch (NotValidCurrentPageException $e) {
                throw new NotFoundHttpException();
            }
        }

        return $this->render($this->getTemplate(), array(
            'pagerfanta'        => $pagerfanta,
            'pagerfantaView'    => $this->getOption('pagerfantaView'),
            'pagerfantaOptions' => $this->getOption('pagerfantaOptions'),
        ));
    }
}
