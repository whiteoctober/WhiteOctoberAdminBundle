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
use WhiteOctober\AdminBundle\Admin\AdminSession;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;

abstract class ListAction extends Action
{
    protected function configure()
    {
        $this
            ->setRoute('list', '/', array(), array('_method' => 'GET'))
            ->setDefaultTemplate('WhiteOctoberAdminBundle::default/list.html.twig')
            ->addOptions(array(
                'sessionParameter'      => 'hash',
                'simpleFilterParameter' => 'q',
                'simpleFilterDefault'   => null,
                'sortParameter'         => 'sort',
                'orderParameter'        => 'order',
                'sortDefault'           => null,
                'orderDefault'          => 'asc',
                'maxPerPage'            => 10,
                'headerTemplates'       => array(),
                'pagerfantaView'        => 'white_october_admin',
                'pagerfantaOptions'     => array(),
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

        $this->initQuery();

        // clear filter
        if ($request->query->get('clearFilter')) {
            $adminSession->remove(array('filter', 'sort', 'order', 'page'));
        }

        // simple filter
        $simpleFilter = $request->query->get(
            $this->getOption('simpleFilterParameter'),
            $adminSession->get('simpleFilter', $this->getOption('simpleFilterDefault'))
        );
        if ($simpleFilter) {
            $this->applySimpleFilter($simpleFilter);

            $adminSession->set('simpleFilter', $simpleFilter);
            $adminSession->remove(array('sort', 'order', 'page'));
        }

        // sort
        $sort = $request->query->get($this->getOption('sortParameter'), $adminSession->get('sort', $this->getOption('sortDefault')));
        $order = $request->query->get($this->getOption('orderParameter'), $adminSession->get('order', $this->getOption('orderDefault')));
        if ($sort && $order) {
            if ($this->getFields()->has($sort) && $this->getFields()->get($sort)->getOption('sortable')) {
                $this->applySort($sort, $order);

                $adminSession->set('sort', $sort);
                $adminSession->set('order', $order);
            }
        }

        // pagerfanta
        $pagerfantaAdapter = $this->createPagerfantaAdapter();
        $pagerfanta = new Pagerfanta($pagerfantaAdapter);
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

    protected function getSimpleFilterFields()
    {
        $fields = array();
        foreach ($this->getAdmin()->getFields() as $field) {
            if ($field->hasOption('simpleFilter') && $field->getOption('simpleFilter')) {
                $fields[] = $field->getName();
            }
        }

        return $fields;
    }

    abstract protected function initQuery();

    abstract protected function applySimpleFilter($filter);

    abstract protected function applySort($sort, $order);

    abstract protected function createPagerfantaAdapter();
}
