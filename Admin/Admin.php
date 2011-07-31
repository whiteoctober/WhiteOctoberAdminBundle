<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Admin;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Action\ActionInterface;
use WhiteOctober\AdminBundle\Action\BatchAction;

/**
 * Admin.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class Admin extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function preConfigure()
    {
        $module = $this;

        // actions
        $this->addAction($this->getCRUDActionCollection());
        $this->addAction(new BatchAction());

        // field guessers
        $this->addFieldGuesser('validator');

        // admin session
        $this->addOption('adminSessionParameterName', 'as');

        // options
        $this->addOption('filterQueryCallbacks', array());
        $this->addOption('createDataCallbacks', array());

        // create form closure
        $this->addOption('createFormClosure', function (ActionInterface $action, $data) use ($module) {
            $formFactory = $module->getContainer()->get('form.factory');
            $formBuilder = $formFactory->createBuilder('form', $data);
            foreach ($action->getFields() as $field) {
                $type = $field->hasOption('form_type') ? $field->getOption('form_type') : null;
                $options = $field->hasOption('form_options') ? $field->getOption('form_options') : array();
                $options['label'] = $field->getLabel();
                $formBuilder->add($field->getName(), $type, $options);
            }
            $form = $formBuilder->getForm();

            return $form;
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function postConfigure()
    {
        // admin session
        if ($this->container->isScopeActive('request')) {
            $parameterName = $this->getOption('adminSessionParameterName');

            $adminSession = new AdminSession($this->container->get('request'), $this->container->get('session'), $parameterName);
            $this->addOption('adminSession', $adminSession);

            $this->addParameterToPropagate($parameterName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createView()
    {
        return new AdminView($this);
    }

    /**
     * Transform an string in a filter.
     *
     * @param string $filter The filter in string.
     *
     * @return FilterInterface The filter.
     *
     * @throws \RuntimeException If the filter cannot be transformed.
     */
    public function transformFilter($filter)
    {
        throw new \RuntimeException(sprintf('The filter "%s" cannot be transformed.', $filter));
    }

    /**
     * Returns the CRUDActionCollection.
     */
    abstract protected function getCRUDActionCollection();
}
