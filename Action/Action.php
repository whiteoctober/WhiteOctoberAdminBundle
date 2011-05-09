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

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\ParameterBag;
use WhiteOctober\AdminBundle\Admin\Admin;
use WhiteOctober\AdminBundle\Field\FieldBag;
use WhiteOctober\AdminBundle\Field\FieldConfigurator;
use WhiteOctober\AdminBundle\Guesser\FieldGuessador;

abstract class Action extends ContainerAware
{
    private $admin;
    private $options;
    private $name;
    private $namespace;
    private $routeNameSuffix;
    private $routePatternSuffix;
    private $routeDefaults;
    private $routeRequirements;
    private $defaultTemplate;
    private $dependences;
    private $fields;

    public function __construct(array $options = array())
    {
        $this->options = array();
        $this->routeDefaults = array();
        $this->routeRequirements = array();
        $this->dependences = array();

        $this->configure();

        if (!$this->name) {
            throw new \RuntimeException('An action must have name.');
        }
        if (!$this->routeNameSuffix) {
            throw new \RuntimeException('An action must have route name suffix.');
        }
        if (!$this->routePatternSuffix) {
            throw new \RuntimeException('An action must have route name suffix.');
        }

        if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new \InvalidArgumentException(sprintf('The action "%s" does not support the following options "".', get_class($this), implode(', ', $diff)));
        }
    }

    abstract protected function configure();

    public function configureActionsVars(ParameterBag $actionVars)
    {
    }

    public function setAdmin(Admin $admin)
    {
        $this->admin = $admin;
    }

    public function getAdmin()
    {
        return $this->admin;
    }

    public function getDataClass()
    {
        return $this->admin->getDataClass();
    }

    public function getActionsVars()
    {
        return $this->admin->getActionsVars();
    }

    public function mergeOptions(array $options = array())
    {
        $this->options = array_merge_recursive($this->options, $options);
    }

    protected function setOptions(array $options)
    {
        $this->options = $options;
    }

    protected function addOption($name, $defaultValue)
    {
        $this->options[$name] = $defaultValue;
    }

    protected function addOptions(array $options)
    {
        foreach ($options as $name => $defaultValue) {
            $this->addOption($name, $defaultValue);
        }
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    public function getOption($name)
    {
        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        return $this->options[$name];
    }

    public function setName($name)
    {
        if (false !== $pos = strrpos($name, '.')) {
            $namespace = substr($name, 0, $pos);
            $name = substr($name, $pos + 1);
        } else {
            $namespace = $this->namespace;
        }

        if (!$name) {
            throw new \InvalidArgumentException('An action must have a name.');
        }

        $this->namespace = $namespace;
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getFullName()
    {
        return $this->getNamespace() ? $this->getNamespace().'.'.$this->getName() : $this->getName();
    }

    public function setRouteNameSuffix($routeNameSuffix)
    {
        $this->routeNameSuffix = $routeNameSuffix;

        return $this;
    }

    public function getRouteNameSuffix()
    {
        return $this->routeNameSuffix;
    }

    public function setRoutePatternSuffix($routePatternSuffix)
    {
        $this->routePatternSuffix = $routePatternSuffix;

        return $this;
    }

    public function getRoutePatternSuffix()
    {
        return $this->routePatternSuffix;
    }

    public function setRouteDefaults(array $routeDefaults)
    {
        $this->routeDefaults = $routeDefaults;

        return $this;
    }

    public function getRouteDefaults()
    {
        return $this->routeDefaults;
    }

    public function setRouteRequirements(array $routeRequirements)
    {
        $this->routeRequirements = $routeRequirements;

        return $this;
    }

    public function getRouteRequirements()
    {
        return $this->routeRequirements;
    }

    public function setRoute($routeNameSuffix, $routePatternSuffix, array $routeDefaults = array(), array $routeRequirements = array())
    {
        $this->routeNameSuffix = $routeNameSuffix;
        $this->routePatternSuffix = $routePatternSuffix;
        $this->routeDefaults = $routeDefaults;
        $this->routeRequirements = $routeRequirements;

        return $this;
    }

    public function setDefaultTemplate($defaultTemplate)
    {
        $this->defaultTemplate = $defaultTemplate;

        return $this;
    }

    public function getDefaultTemplate()
    {
        return $this->defaultTemplate;
    }

    public function getFieldGuessers()
    {
        return $this->admin->getFieldGuessers();
    }

    public function setDependences(array $dependences)
    {
        $this->dependences = $dependences;

        return $this;
    }

    public function getDependences()
    {
        return $this->dependences;
    }


    public function getFields()
    {
        if (null === $this->fields) {
            $fields = array();
            foreach ($this->admin->getFields() as $field) {
                $fields[$field->getName()] = clone $field;
            }

            $fieldConfigurator = new FieldConfigurator($fields);
            $this->admin->configureFieldsByAction($this, $fieldConfigurator);
            $fields = $fieldConfigurator->all();

            $dataClass = $this->getDataClass();
            $guessador = new FieldGuessador($this->getFieldGuessers());
            foreach ($fields as $field) {
                $guessOptions = $guessador->guessOptions($dataClass, $field->getName());
                $field->appendOptions($guessOptions);
            }

            $this->fields = new FieldBag($fields);
        }

        return $this->fields;
    }

    public function getTemplate()
    {
        return $this->hasOption('template') ? $this->getOption('template') : $this->getDefaultTemplate();
    }

    public function getDataValue($data, $fieldName)
    {
        return $data->get($fieldName);
    }

    abstract public function executeController();

    public function renderView($template, array $parameters = array())
    {
        $parameters['_admin'] = $this->admin->createView();
        $parameters['_action'] = $this->createView();

        return $this->container->get('templating')->render($template, $parameters);
    }

    public function render($template, array $parameters = array(), $response = null)
    {
        $parameters['_admin'] = $this->admin->createView();
        $parameters['_action'] = $this->createView();

        return $this->container->get('templating')->renderResponse($template, $parameters, $response);
    }

    public function generateUrl($routeNameSuffix, array $parameters = array(), $absolute = false)
    {
        return $this->admin->generateUrl($routeNameSuffix, $parameters, $absolute);
    }

    protected function createFormFromFields(array $fields)
    {
        $formFactory = $this->container->get('form.factory');
        $formBuilder = $formFactory->createBuilder('form', null, array(
            'data_class' => $this->getDataClass(),
        ));
        foreach ($fields as $field) {
            $type = $field->hasOption('form_type') ? $field->getOption('form_type') : null;
            $options = $field->hasOption('form_options') ? $field->getOption('form_options') : array();
            $formBuilder->add($field->getName(), $type, $options);
        }
        $form = $formBuilder->getForm();

        return $form;
    }

    public function createView()
    {
        return new ActionView($this);
    }

    public function has($id)
    {
        return $this->container->has($id);
    }

    public function get($id)
    {
        return $this->container->get($id);
    }
}
