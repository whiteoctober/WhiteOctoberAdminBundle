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

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAware;
use WhiteOctober\AdminBundle\Action\Action;
use WhiteOctober\AdminBundle\Action\ActionCollection;
use WhiteOctober\AdminBundle\Field\Field;
use WhiteOctober\AdminBundle\Field\FieldConfigurator;

abstract class Admin extends ContainerAware
{
    private $name;
    private $dataClass;
    private $routePatternPrefix;
    private $routeNamePrefix;
    private $baseTemplate;

    private $rawFields;
    private $fields;

    private $rawFieldGuessers;
    private $fieldGuessers;

    private $rawActions;
    private $actions;

    public function __construct()
    {
        $this->rawFields = array();
        $this->rawFieldGuessers = array();
        $this->rawActions = array();

        $this->preConfigure();
        $this->configure();
        $this->postConfigure();

        if (!$this->dataClass) {
            throw new \RuntimeException('The data class cannot be empty.');
        }

        if (!$this->name) {
            $this->name = $this->getDataClassName();
        }
    }

    protected function preConfigure()
    {
        $this->addFieldGuesser('validator');
    }

    abstract protected function configure();

    protected function postConfigure()
    {
    }

    public function configureFieldsByAction(Action $action, FieldConfigurator $fieldConfigurator)
    {
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDataClass($dataClass)
    {
        $this->dataClass = $dataClass;

        return $this;
    }

    public function getDataClass()
    {
        return $this->dataClass;
    }

    public function getDataClassName()
    {
        return substr($this->getDataClass(), strrpos($this->getDataClass(), '\\')+1);
    }

    public function setRoutePatternPrefix($routePatternPrefix)
    {
        $this->routePatternPrefix = $routePatternPrefix;

        return $this;
    }

    public function getRoutePatternPrefix()
    {
        if (null === $this->routeNamePrefix) {
            $this->routePatternPrefix = '/'.$this->urlize(get_class($this));
        }

        return $this->routePatternPrefix;
    }

    public function setRouteNamePrefix($routeNamePrefix)
    {
        $this->routeNamePrefix = $routeNamePrefix;

        return $this;
    }

    public function getRouteNamePrefix()
    {
        if (null === $this->routeNamePrefix) {
            $this->routeNamePrefix = $this->urlize(get_class($this), '_');
        }

        return $this->routeNamePrefix;
    }

    public function setBaseTemplate($baseTemplate)
    {
        $this->baseTemplate = $baseTemplate;

        return $this;
    }

    public function getBaseTemplate()
    {
        if (null === $this->baseTemplate) {
            $this->baseTemplate = 'WhiteOctoberAdminBundle::layout.html.twig';
        }

        return $this->baseTemplate;
    }

    public function addField($name, array $options = array())
    {
        $this->rawFields[$name] = $options;

        return $this;
    }

    public function addFields(array $fields)
    {
        foreach ($fields as $name => $options) {
            if (is_integer($name) && is_string($options)) {
                $name = $options;
                $options = array();
            }
            $this->addField($name, $options);
        }

        return $this;
    }

    public function getFields()
    {
        if (null === $this->fields) {
            $this->initializeFields();
        }

        return $this->fields;
    }

    public function hasField($name)
    {
        if (null === $this->fields) {
            $this->initializeFields();
        }

        return isset($this->fields[$name]);
    }

    public function getField($name)
    {
        if (!$this->hasField($name)) {
            throw new \InvalidArgumentException(sprintf('The field "%s" does not exist.', $name));
        }

        return $this->fields[$name];
    }

    public function addFieldGuesser($fieldGuesser)
    {
        $this->rawFieldGuessers[] = $fieldGuesser;

        return $this;
    }

    public function addFieldGuessers(array $fieldGuessers)
    {
        foreach ($fieldGuessers as $fieldGuesser) {
            $this->addFieldGuesser($fieldGuesser);
        }

        return $this;
    }

    public function getFieldGuessers()
    {
        if (null == $this->fieldGuessers) {
            $this->initializeFieldGuessers();
        }

        return $this->fieldGuessers;
    }

    public function addAction($action)
    {
        if (!is_string($action) && !$action instanceof Action && !$action instanceof ActionCollection) {
            throw new \InvalidArgumentException('Some action is not an string nor an instance of Action nor ActionCollection.');
        }

        $this->rawActions[is_string($action) ? $action : $action->getName()] = $action;

        return $this;
    }

    public function addActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }

        return $this;
    }

    public function getActions()
    {
        if (null === $this->actions) {
            $this->initializeActions();
        }

        return $this->actions;
    }

    public function hasAction($fullName)
    {
        if (null === $this->actions) {
            $this->initializeActions();
        }

        return isset($this->actions[$fullName]);
    }

    public function getAction($fullName)
    {
        if (!$this->hasAction($fullName)) {
            throw new \InvalidArgumentException(sprintf('The action "%s" does not exist.', $fullName));
        }

        return $this->actions[$fullName];
    }

    public function getDataFieldValue($data, $fieldName)
    {
        if (method_exists($data, 'get')) {
            return $data->get($fieldName);
        }

        return $data->{'get'.Container::camelize($fieldName)}();
    }

    public function getAccessRouteNameSuffix()
    {
        $action = null;
        foreach ($this->getActions() as $action) {
            break;
        }
        if (!$action) {
            throw new \RuntimeException(sprintf('There is no access route.'));
        }

        return $action->getRouteNameSuffix();
    }

    public function generateUrl($routeNameSuffix, array $parameters = array(), $absolute = false)
    {
        return $this->container->get('router')->generate($this->getRouteNamePrefix().'_'.$routeNameSuffix, $parameters, $absolute);
    }

    private function urlize($string, $sep = '-')
    {
        $string = preg_replace('/\\\/', $sep, $string);
        $string = strtolower($string);

        return $string;
    }

    private function initializeFields()
    {
        $fields = array();
        foreach ($this->rawFields as $name => $options) {
            $fields[$name] = new Field($name, $options);
        }
        $this->fields = $fields;
        $this->rawFields = null;
    }

    private function initializeFieldGuessers()
    {
        $fieldGuessers = array();
        foreach ($this->rawFieldGuessers as $rawFieldGuesser) {
            if (is_string($rawFieldGuesser)) {
                $rawFieldGuesser = $this->container->get('white_october_admin.admin_factory')->getFieldGuesser($rawFieldGuesser);
            }
            $fieldGuessers[] = $rawFieldGuesser;
        }
        $this->fieldGuessers = $fieldGuessers;
        $this->rawFieldGuessers = null;
    }

    private function initializeActions()
    {
        $actions = array();
        foreach ($this->cleanActions($this->rawActions) as $action) {
            if (isset($actions[$action->getFullName()])) {
                throw new \RuntimeException(sprintf('You cannot use the action "%s" more than once.', $action->getName()));
            }
            $action->setAdmin($this);
            $action->setContainer($this->container);
            foreach ($action->getDependences() as $actionFullName => $options) {
                if (!isset($actions[$actionFullName])) {
                    throw new \RuntimeException(sprintf('The action "%s" does not exist.', $actionFullName));
                }
                $actions[$actionFullName]->mergeOptions($options);
            }
            $actions[$action->getFullName()] = $action;
        }
        $this->actions = $actions;
        $this->rawActions = null;
    }

    private function cleanActions(array $inputActions)
    {
        $actions = array();
        foreach ($inputActions as $action) {
            // in the container
            if (is_string($action)) {
                $action = clone $this->container->get('white_october_admin.action_factory')->get($action);
            }

            // normal action
            if ($action instanceof Action) {
                $actions[] = $action;
                continue;
            }
            // action collection
            if ($action instanceof ActionCollection) {
                $actions = array_merge($actions, $this->cleanActions($action->all()));
                continue;
            }

            // invalid
            throw new \RuntimeException('The action is not an instance of Action nor ActionCollection.');
        }

        return $actions;
    }

    public function createView()
    {
        return new AdminView($this);
    }
}
