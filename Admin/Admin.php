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
use Symfony\Component\HttpFoundation\ParameterBag;
use WhiteOctober\AdminBundle\Action\Action;
use WhiteOctober\AdminBundle\Action\ActionInterface;
use WhiteOctober\AdminBundle\Action\ActionCollection;
use WhiteOctober\AdminBundle\Field\Field;
use WhiteOctober\AdminBundle\Field\FieldConfigurator;

/**
 * Action.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class Admin extends ContainerAware implements AdminInterface
{
    private $dataClass;
    private $name;
    private $routeNamePrefix;
    private $routePatternPrefix;
    private $parametersToPropagate;
    private $baseTemplate;

    private $rawFields;
    private $fields;

    private $rawFieldGuessers;
    private $fieldGuessers;

    private $rawActions;
    private $actionParsers;
    private $actions;
    private $actionOptionsSets;
    private $actionOptionsProcessors;
    private $actionsVars;

    private $controllerPreExecutes;

    /**
     * Constructor.
     *
     * @throws \RuntimeException If the data class is empty.
     */
    public function __construct()
    {
        $this->parametersToPropagate = array();
        $this->rawFields = array();
        $this->rawFieldGuessers = array();
        $this->rawActions = array();
        $this->actionParsers = array();
        $this->actionOptionsSets = array();
        $this->actionOptionsProcessors = array();
        $this->controllerPreExecutes = array();

        $this->addFieldGuesser('validator');

        $this->preConfigure();
        $this->configure();
        $this->postConfigure();

        if (!$this->dataClass) {
            throw new \RuntimeException('The data class cannot be empty.');
        }

        if (!$this->name) {
            $this->name = $this->getDataClassName();
        }

        if (null === $this->routePatternPrefix) {
            $this->routePatternPrefix = '/'.$this->urlize(get_class($this));
        }

        if (null === $this->routeNamePrefix) {
            $this->routeNamePrefix = $this->urlize(get_class($this), '_');
        }
    }

    /**
     * Pre configures the admin.
     */
    protected function preConfigure()
    {
    }

    /**
     * Configures the admin.
     */
    abstract protected function configure();

    /**
     * Post configures the admin.
     */
    protected function postConfigure()
    {
    }

    /**
     * Configures the fields for an action.
     *
     * @param ActionInterface   $action An action.
     * @param FieldConfigurator $fieldConfigurator A FieldConfigurator instance.
     */
    public function configureFieldsByAction(ActionInterface $action, FieldConfigurator $fieldConfigurator)
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

    /**
     * Sets the data class.
     *
     * @param string $dataClass The data class.
     *
     * @return AdminInterface The admin (fluent interface).
     */
    public function setDataClass($dataClass)
    {
        $this->dataClass = $dataClass;

        return $this;
    }

    /**
     * Returns the data class.
     */
    public function getDataClass()
    {
        return $this->dataClass;
    }

    public function getDataClassName()
    {
        return substr($this->getDataClass(), strrpos($this->getDataClass(), '\\')+1);
    }

    /**
     * Sets the route name prefix.
     *
     * @param string $routeNamePrefix The route name prefix.
     *
     * @return AdminInterface The admin (fluent interface).
     */
    public function setRouteNamePrefix($routeNamePrefix)
    {
        $this->routeNamePrefix = $routeNamePrefix;

        return $this;
    }

    /**
     * Returns the route name prefix.
     *
     * @return string The route name prefix.
     */
    public function getRouteNamePrefix()
    {
        return $this->routeNamePrefix;
    }

    /**
     * Sets the route pattern prefix.
     *
     * @param string $routePatternPrefix The route pattern preffix.
     *
     * @return AdminInterface The admin (fluent interface).
     */
    public function setRoutePatternPrefix($routePatternPrefix)
    {
        $this->routePatternPrefix = $routePatternPrefix;

        return $this;
    }

    /**
     * Returns the route pattern prefix.
     *
     * @return string The route pattern prefix.
     */
    public function getRoutePatternPrefix()
    {
        return $this->routePatternPrefix;
    }

    /**
     * Adds a parameter to propagate.
     *
     * @param string $parameter The parameter.
     *
     * @return AdminInterface The admin (fluent interface).
     */
    public function addParameterToPropagate($parameter)
    {
        $this->parametersToPropagate[] = $parameter;

        return $this;
    }

    /**
     * Adds parameters to propagate.
     *
     * @param array $parameters The parameters.
     *
     * @return AdminInterface The admin (fluent interface).
     */
    public function addParametersToPropagate(array $parameters)
    {
        foreach ($parameters as $parameter) {
            $this->addParameterToPropagate($parameter);
        }

        return $this;
    }

    /**
     * Sets the parameters to propagate.
     *
     * @param array $parameters The parameters.
     *
     * @return AdminInterface The admin (fluent interface).
     */
    public function setParametersToPropagate(array $parameters)
    {
        $this->parametersToPropagate = array();
        $this->addParametersToPropagate($parameters);

        return $this;
    }

    /**
     * Returns the parameters to propagate.
     *
     * @return array The parameters to propagate.
     */
    public function getParametersToPropagate()
    {
        return $this->parametersToPropagate;
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

    public function addActionParser(\Closure $parser)
    {
        $this->actionParsers[] = $parser;

        return $this;
    }

    public function getActionParsers()
    {
        return $this->actionParsers;
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

    public function getActionsVars()
    {
        return $this->actionsVars;
    }

    public function setActionOption($actionName, $optionName, $optionValue)
    {
        $this->actionOptionsSets[$actionName][$optionName] = $optionValue;

        return $this;
    }

    public function addActionOptionProcessor($actionName, $optionName, \Closure $processor)
    {
        $this->actionOptionsProcessors[$actionName][$optionName][] = $processor;

        return $this;
    }

    public function addControllerPreExecute(\Closure $controllerPreExecute)
    {
        $this->controllerPreExecutes[] = $controllerPreExecute;

        return $this;
    }

    public function getControllerPreExecutes()
    {
        return $this->controllerPreExecutes;
    }

    public function getDataFieldValue($data, $fieldName)
    {
        return $data->{'get'.ucfirst($fieldName)}();
    }

    public function generateAccessUrl()
    {
        $action = null;
        foreach ($this->getActions() as $action) {
            break;
        }
        if (!$action) {
            throw new \RuntimeException(sprintf('There is no access route.'));
        }

        return $this->generateUrl($action->getRouteNameSuffix());
    }

    public function generateUrl($routeNameSuffix, array $parameters = array(), $absolute = false)
    {
        if ($this->parametersToPropagate) {
            $request = $this->container->get('request');
            foreach ($this->parametersToPropagate as $parameter) {
                if (!isset($parameters[$parameter]) && $value = $request->get($parameter)) {
                    $parameters[$parameter] = $value;
                }
            }
        }

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
        $this->actionsVars = new ParameterBag();

        $actions = array();
        foreach ($this->cleanActions($this->rawActions) as $action) {
            if (isset($actions[$action->getFullName()])) {
                throw new \RuntimeException(sprintf('You cannot use the action "%s" more than once.', $action->getName()));
            }
            $action->setAdmin($this);
            $action->setContainer($this->container);
            foreach ($action->getActionDependences() as $actionName => $options) {
                $dependenceAction = $this->findAction($actions, $actionName);
                $dependenceAction->mergeOptions($options);
            }

            $actions[$action->getFullName()] = $action;
        }

        // action parsers
        foreach ($this->actionParsers as $parser) {
            $parser($actions);
        }

        // action options sets
        foreach ($this->actionOptionsSets as $actionName => $options) {
            $action = $this->findAction($actions, $actionName);

            foreach ($options as $name => $value) {
                $action->setOption($name, $value);
            }
        }

        // action options processors
        foreach ($this->actionOptionsProcessors as $actionName => $options) {
            $action = $this->findAction($actions, $actionName);

            foreach ($options as $name => $processors) {
                $value = $action->getOption($name);
                foreach ($processors as $processor) {
                    $value = $processor($value);
                }
                $action->setOption($name, $value);
            }
        }

        // action vars
        foreach ($actions as $action) {
            $action->configureActionsVars($this->actionsVars);
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

                // action
                if ($this->container->get('white_october_admin.action_factory')->has($action)) {
                    $action = clone $this->container->get('white_october_admin.action_factory')->get($action);
                } else {
                    // collection
                    if ($this->container->get('white_october_admin.action_collection_factory')->has($action)) {
                        $action = clone $this->container->get('white_october_admin.action_collection_factory')->get($action);
                    }
                }
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
        $view = new AdminView($this);
        $view->setContainer($this->container);

        return $view;
    }

    private function findAction(array $actions, $actionName)
    {
        // by full name
        if (isset($actions[$actionName])) {
            return $actions[$actionName];
        }
        // by name
        foreach ($actions as $action) {
            if ($action->getName() == $actionName) {
                return $action;
            }
        }
        // action does not exist
        throw new \RuntimeException(sprintf('The action "%s" does not exist.', $actionName));
    }
}
