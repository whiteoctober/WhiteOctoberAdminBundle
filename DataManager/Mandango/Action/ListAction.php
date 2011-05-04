<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Mandango\Action;

use WhiteOctober\AdminBundle\Action\Action;
use WhiteOctober\AdminBundle\Admin\AdminSession;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pagerfanta\Adapter\MandangoAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;

class ListAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('mandango.list')
            ->setRoute('list', '/', array(), array('_method' => 'GET'))
            ->setDefaultTemplate('WhiteOctoberAdminBundle::default/list.html.twig')
            ->addOptions(array(
                'sessionParameter'  => 'hash',
                'sortParameter'     => 'sort',
                'orderParameter'    => 'order',
                'sortDefault'       => null,
                'orderDefault'      => 'asc',
                'maxPerPage'        => 10,
                'pagerfantaView'    => 'white_october_admin',
                'pagerfantaOptions' => array(),
            ))
        ;
    }

    public function configureActionsVars(ParameterBag $actionsVars)
    {
        if ($this->container->isScopeActive('request')) {
            $adminSession = new AdminSession($this->container->get('request'), $this->container->get('session'), $this->getOption('sessionParameter'));
            $actionsVars->set('admin_session', $adminSession);
        }
    }

    public function executeController()
    {
        $request = $this->get('request');
        $adminSession = $this->getActionsVars()->get('admin_session');

        $dataClass = $this->getDataClass();
        $query = $dataClass::getRepository()->createQuery();

        // sort
        $sort = $request->query->get($this->getOption('sortParameter'), $adminSession->get('sort', $this->getOption('sortDefault')));
        $order = $request->query->get($this->getOption('orderParameter'), $adminSession->get('order', $this->getOption('orderDefault')));
        if ($sort && $order) {
            if ($this->getFields()->has($sort) && $this->getFields()->get($sort)->getOption('sortable')) {
                $query->sort(array($sort => 'asc' == $order ? \MongoCollection::ASCENDING : \MongoCollection::DESCENDING));
            }

            $adminSession->set('sort', $sort);
            $adminSession->set('order', $order);
        }

        // pagerfanta
        $adapter = new MandangoAdapter($query);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($this->getOption('maxPerPage'));
        if ($page = $request->query->get('page', $this->getActionsVars()->get('admin_session')->get('page'))) {
            try {
                $pagerfanta->setCurrentPage($page);

                $this->getActionsVars()->get('admin_session')->set('page', $page);
            } catch (NotValidCurrentPageException $e) {
                throw new NotFoundHttpException();
            }
        }

        return $this->render($this->getTemplate(), array(
            'sort'  => $sort,
            'order' => $order,
            'pagerfanta'        => $pagerfanta,
            'pagerfantaView'    => $this->getOption('pagerfantaView'),
            'pagerfantaOptions' => $this->getOption('pagerfantaOptions'),
        ));
    }
}
