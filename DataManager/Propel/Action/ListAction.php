<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Propel\Action;

use WhiteOctober\AdminBundle\Action\Action;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pagerfanta\Adapter\PropelAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;

/**
 * ListAction for Propel.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class ListAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('propel.list')
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
        $queryClass = $dataClass.'Query';

        $query = $queryClass::create();
        $adapter = new PropelAdapter($query);
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
