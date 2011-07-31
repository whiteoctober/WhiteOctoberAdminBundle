<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Action;

use Pablodip\ModuleBundle\Action\Action;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WhiteOctober\AdminBundle\Filter\FilterInterface;

/**
 * ListAction.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class ListAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('white_october_admin.list')
            ->setRoute('list', '', array(), array('_method' => 'GET'))
            ->addOptions(array(
                'title'                   => 'list',
                'title_domain'            => 'white_october_admin',
                'heading'                 => 'list',
                'heading_domain'          => 'white_october_admin',
                'maxPerPage'              => 10,
                'simpleFilterParameter'   => 'q',
                'simpleFilterDefault'     => null,
                'advancedFilterParameter' => 'advancedFilter',
                'sortParameter'           => 'sort',
                'orderParameter'          => 'order',
                'sortDefault'             => null,
                'orderDefault'            => 'asc',
                'actions'                 => array(),
                'dataActions'             => array(),
                'template'                => 'WhiteOctoberAdminBundle::actions/list.html.twig',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function executeController()
    {
        $request = $this->get('request');
        $adminSession = $this->getModuleOption('adminSession');

        // query
        $query = $this->getModuleOption('createQueryClosure')->__invoke();

        // clear
        if ($request->query->get('clear')) {
            $adminSession->remove(array('simpleFilter', 'advancedFilter', 'sort', 'order', 'page'));
        }

        // simple filter
        $simpleFilterFields = $this->getSimpleFilterFields();
        $simpleFilterEnabled = (Boolean) count($simpleFilterFields);
        $simpleFilter = null;
        if ($simpleFilterEnabled) {
            $simpleFilter = $request->query->get(
                $this->getOption('simpleFilterParameter'),
                $adminSession->get('simpleFilter', $this->getOption('simpleFilterDefault'))
            );
            if ($simpleFilter) {
                $this->applySimpleFilter($query, $simpleFilterFields, $simpleFilter);

                $adminSession->set('simpleFilter', $simpleFilter);
                $adminSession->remove(array('sort', 'order', 'page'));
            }
        }

        // advanced filter
        $advancedFilters = $this->getAdvancedFilters();
        $advancedFilterEnabled = (Boolean) count($advancedFilters);
        $advancedFilterForm = null;
        if ($advancedFilterEnabled) {
            $formBuilder = $this->get('form.factory')->createNamedBuilder('form', $this->getOption('advancedFilterParameter'));
            foreach ($advancedFilters as $fieldName => $advancedFilter) {
                $filterFormBuilder = $this->get('form.factory')->createNamedBuilder('form', $fieldName);
                $advancedFilter->buildForm($filterFormBuilder);
                $formBuilder->add($filterFormBuilder);
            }
            $advancedFilterForm = $formBuilder->getForm();

            $advancedFilterData = $request->query->get($this->getOption('advancedFilterParameter'), $adminSession->get('advancedFilter'));
            if ($advancedFilterData) {
                $advancedFilterForm->bind($advancedFilterData);
                if ($advancedFilterForm->isValid()) {
                    $this->applyAdvancedFilter($query, $advancedFilters, $advancedFilterForm->getData());

                    $adminSession->set('advancedFilter', $advancedFilterData);
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
        if ($page = $request->query->get('page', $adminSession->get('page'))) {
            try {
                $pagerfanta->setCurrentPage($page);

                $adminSession->set('page', $page);
            } catch (NotValidCurrentPageException $e) {
                throw new NotFoundHttpException();
            }
        }

        return $this->render($this->getOption('template'), array(
            'pagerfanta'              => $pagerfanta,
            'simple_filter_enabled'   => $simpleFilterEnabled,
            'simple_filter'           => $simpleFilter,
            'advanced_filter_enabled' => $advancedFilterEnabled,
            'advanced_filter_form'    => $advancedFilterForm ? $advancedFilterForm->createView() : null,
            'sort'                    => $sort,
            'order'                   => $order,
        ));
    }

    private function getSimpleFilterFields()
    {
        $fields = array();
        foreach ($this->getModule()->getFields() as $field) {
            if ($field->hasOption('simple_filter') && $field->getOption('simple_filter')) {
                $fields[] = $field->getName();
            }
        }

        return $fields;
    }

    private function getAdvancedFilters()
    {
        $filters = array();
        foreach ($this->getFields() as $field) {
            if ($field->hasOption('advanced_filter') && $filter = $field->getOption('advanced_filter')) {
                if (!$filter instanceof FilterInterface) {
                    if ($field->hasOption('advanced_filter_type')) {
                        $filter = $this->getModule()->transformFilter($field->getOption('advanced_filter_type'));
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

    /*
     * List.
     */
    abstract protected function applySimpleFilter($query, array $fields, $filter);

    abstract protected function applyAdvancedFilter($query, array $filters, array $data);

    abstract protected function applySort($query, $sort, $order);

    abstract protected function createPagerfantaAdapter($query);
}
