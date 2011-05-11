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
use WhiteOctober\AdminBundle\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;

abstract class ListAction extends Action
{
    protected function configure()
    {
        $this
            ->setRoute('list', '', array(), array('_method' => 'GET'))
            ->setDefaultTemplate('WhiteOctoberAdminBundle::default/list.html.twig')
            ->addOptions(array(
                'sessionParameter'        => 'hash',
                'filterQueryCallbacks'    => array(),
                'findDataByIdCallbacks'   => array(),
                'createDataCallbacks'     => array(),
                'simpleFilterParameter'   => 'q',
                'simpleFilterDefault'     => null,
                'advancedFilterParameter' => 'advancedFilter',
                'sortParameter'           => 'sort',
                'orderParameter'          => 'order',
                'sortDefault'             => null,
                'orderDefault'            => 'asc',
                'maxPerPage'              => 10,
                'headerTemplates'         => array(),
                'pagerfantaView'          => 'white_october_admin',
                'pagerfantaOptions'       => array(),
            ))
        ;
    }

    public function configureActionsVars(ParameterBag $actionsVars)
    {
        if ($this->container->isScopeActive('request')) {
            $adminSession = new AdminSession($this->container->get('request'), $this->container->get('session'), $this->getOption('sessionParameter'));
            $actionsVars->set('admin_session', $adminSession);
        }

        $filterQueryClosure = $this->getFilterQueryClosure();
        $filterQueryCallbacks = $this->getOption('filterQueryCallbacks');
        $actionsVars->set('filterQueryClosure', function () use ($filterQueryClosure, $filterQueryCallbacks) {
            $args = func_get_args();
            $args[] = $filterQueryCallbacks;
            return call_user_func_array($filterQueryClosure, $args);
        });

        $createDataClosure = $this->getCreateDataClosure();
        $createDataCallbacks = $this->getOption('createDataCallbacks');
        $actionsVars->set('createDataClosure', function () use ($createDataClosure, $createDataCallbacks) {
            $args = func_get_args();
            $args[] = $createDataCallbacks;
            return call_user_func_array($createDataClosure, $args);
        });

        $findDataByIdClosure = $this->getFindDataByIdClosure();
        $findDataByIdCallbacks = $this->getOption('findDataByIdCallbacks');
        $actionsVars->set('findDataByIdClosure', function () use ($findDataByIdClosure, $findDataByIdCallbacks) {
            $args = func_get_args();
            $args[] = $findDataByIdCallbacks;
            return call_user_func_array($findDataByIdClosure, $args);
        });

        $saveDataClosure = $this->getSaveDataClosure();
        $actionsVars->set('saveDataClosure', function () use ($saveDataClosure) {
            return call_user_func_array($saveDataClosure, func_get_args());
        });

        $deleteDataClosure = $this->getSaveDataClosure();
        $actionsVars->set('deleteDataClosure', function () use ($deleteDataClosure) {
            return call_user_func_array($deleteDataClosure, func_get_args());
        });
    }

    public function executeController()
    {
        $request = $this->get('request');
        $adminSession = $this->getActionsVars()->get('admin_session');

        $query = $this->createQuery();

        $filterQueryClosure = $this->getActionsVars()->get('filterQueryClosure');
        $filterQueryClosure($query);

        // clear filter
        if ($request->query->get('clearFilter')) {
            $adminSession->remove(array('simpleFilter', 'sort', 'order', 'page'));
        }

        // simple filter
        $simpleFilterEnabled = (Boolean) count($this->getSimpleFilterFields());
        $simpleFilter = null;
        if ($simpleFilterEnabled) {
            $simpleFilter = $request->query->get(
                $this->getOption('simpleFilterParameter'),
                $adminSession->get('simpleFilter', $this->getOption('simpleFilterDefault'))
            );
            if ($simpleFilter) {
                $this->applySimpleFilter($query, $simpleFilter);

                $adminSession->set('simpleFilter', $simpleFilter);
                $adminSession->remove(array('sort', 'order', 'page'));
            }
        }

        // advanced filter
        $advancedFilters = $this->getAdvancedFilters();
        $advancedFilterEnabled = (Boolean) count($advancedFilters);
        $advancedFilterForm = null;
        if ($advancedFilters) {
            $formBuilder = $this->get('form.factory')->createNamedBuilder('form', $this->getOption('advancedFilterParameter'));
            foreach ($advancedFilters as $fieldName => $advancedFilter) {
                $filterFormBuilder = $this->get('form.factory')->createNamedBuilder('form', $fieldName);
                $advancedFilter->buildForm($filterFormBuilder);
                $formBuilder->add($filterFormBuilder);
            }
            $advancedFilterForm = $formBuilder->getForm();

            if ($request->query->has($this->getOption('advancedFilterParameter'))) {
                $advancedFilterForm->bindRequest($request);
                if ($advancedFilterForm->isValid()) {
                    $this->applyAdvancedFilter($query, $advancedFilters, $advancedFilterForm->getData());
                }
            }
        }

        // sort
        $sort = $request->query->get($this->getOption('sortParameter'), $adminSession->get('sort', $this->getOption('sortDefault')));
        $order = $request->query->get($this->getOption('orderParameter'), $adminSession->get('order', $this->getOption('orderDefault')));
        if ($sort && $order) {
            if ($this->getFields()->has($sort) && $this->getFields()->get($sort)->getOption('sortable')) {
                $this->applySort($query, $sort, $order);

                $adminSession->set('sort', $sort);
                $adminSession->set('order', $order);
            }
        }

        // pagerfanta
        $pagerfantaAdapter = $this->createPagerfantaAdapter($query);
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
            'simpleFilterEnabled' => $simpleFilterEnabled,
            'simpleFilter'        => $simpleFilter,
            'advancedFilterEnabled' => $advancedFilterEnabled,
            'advancedFilterForm'    => $advancedFilterForm ? $advancedFilterForm->createView() : null,
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

    /*
     * General Closures.
     */
    abstract protected function getFilterQueryClosure();

    abstract protected function getCreateDataClosure();

    abstract protected function getFindDataByIdClosure();

    abstract protected function getSaveDataClosure();

    abstract protected function getDeleteDataClosure();

    /*
     * List.
     */
    abstract protected function createQuery();

    abstract protected function applySimpleFilter($query, $filter);

    abstract protected function applyAdvancedFilter($query, array $filters, array $data);

    abstract protected function applySort($query, $sort, $order);

    abstract protected function createPagerfantaAdapter($query);

    private function getAdvancedFilters()
    {
        $filters = array();
        foreach ($this->getFields() as $field) {
            if ($field->hasOption('advancedFilter') && $filter = $field->getOption('advancedFilter')) {
                if (!$filter instanceof FilterInterface) {
                    if ($field->hasOption('filterType')) {
                        $filter = $this->transformAdvancedFilterType($field->getOption('filterType'));
                    }

                    if (!$filter instanceof FilterInterface) {
                        throw new \RuntimeException('The advanced filters must be instances of FilterInterface.');
                    }
                }
                $filters[$field->getName()] = $filter;
            }
        }

        return $filters;
    }

    protected function transformAdvancedFilterType($type)
    {
    }
}
