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
use WhiteOctober\AdminBundle\Admin\AdminInterface;
use WhiteOctober\AdminBundle\Field\FieldBag;
use WhiteOctober\AdminBundle\Field\FieldConfigurator;
use WhiteOctober\AdminBundle\Guesser\FieldGuessador;

/**
 * Action.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class Action extends ContainerAware implements ActionInterface
{
    private $admin;

    private $name;
    private $namespace;

    private $routeNameSuffix;
    private $routePatternSuffix;
    private $routeDefaults;
    private $routeRequirements;

    private $dependences;
    private $options;

    private $defaultTemplate;
    private $fields;

    /**
     * Constructor.
     *
     * @param array $options An array of options (optional).
     */
    public function __construct(array $options = array())
    {
        $this->routeDefaults = array();
        $this->routeRequirements = array();
        $this->dependences = array();
        $this->options = array();

        $this->configure();

        if (!$this->name) {
            throw new \RuntimeException('An action must have name.');
        }
        if (!$this->routeNameSuffix) {
            throw new \RuntimeException('An action must have route name suffix.');
        }
        if (null === $this->routePatternSuffix) {
            throw new \RuntimeException('An action must have route name suffix.');
        }
    }

    /**
     * Configures the action.
     *
     * You must put in this method at least the name, route name suffix and route pattern suffix.
     */
    abstract protected function configure();

    public function configureActionsVars(ParameterBag $actionVars)
    {
    }

    /**
     * INTERNAL. Sets the admin.
     *
     * @param AdminInterface $admin The admin.
     */
    public function setAdmin(AdminInterface $admin)
    {
        $this->admin = $admin;
    }

    /**
     * Returns the admin.
     *
     * @return AdminInterface The admin.
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Returns the data class of the admin.
     *
     * @return string The data class.
     */
    public function getDataClass()
    {
        return $this->admin->getDataClass();
    }

    public function getActionsVars()
    {
        return $this->admin->getActionsVars();
    }

    /**
     * Sets the name.
     *
     * Sets the name and namespace separating them by the last dot.
     *
     * @param string $name The name.
     *
     * @return Action The action (fluent interface).
     *
     * @throws \InvalidArgumentException If the name is empty.
     */
    public function setName($name)
    {
        if (false !== $pos = strrpos($name, '.')) {
            $namespace = substr($name, 0, $pos);
            $name = substr($name, $pos + 1);
        } else {
            $namespace = null;
        }

        if (!$name) {
            throw new \InvalidArgumentException('An action must have a name.');
        }

        $this->namespace = $namespace;
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the namespace.
     *
     * @return string The namespace.
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Returns the name.
     *
     * @return string The name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the full name (namespace + name).
     *
     * @return string The full name.
     */
    public function getFullName()
    {
        return $this->getNamespace() ? $this->getNamespace().'.'.$this->getName() : $this->getName();
    }

    /**
     * Sets the route name suffix.
     *
     * @param string $routeNameSuffix The route name suffix.
     *
     * @return Action The action (fluent interface).
     */
    public function setRouteNameSuffix($routeNameSuffix)
    {
        $this->routeNameSuffix = $routeNameSuffix;

        return $this;
    }

    /**
     * Returns the route name suffix.
     *
     * @return string The route name suffix.
     */
    public function getRouteNameSuffix()
    {
        return $this->routeNameSuffix;
    }

    /**
     * Sets the route pattern suffix.
     *
     * @param string $routePatternSuffix The route pattern suffix.
     *
     * @return Action The action (fluent interface).
     */
    public function setRoutePatternSuffix($routePatternSuffix)
    {
        $this->routePatternSuffix = $routePatternSuffix;

        return $this;
    }

    /**
     * Returns the route pattern suffix.
     *
     * @return string The route pattern suffix.
     */
    public function getRoutePatternSuffix()
    {
        return $this->routePatternSuffix;
    }

    /**
     * Sets the route defaults.
     *
     * @param array $routeDefaults The route defaults.
     *
     * @return Action The action (fluent interface).
     */
    public function setRouteDefaults(array $routeDefaults)
    {
        $this->routeDefaults = $routeDefaults;

        return $this;
    }

    /**
     * Returns the route defaults.
     *
     * @return array The route defaults.
     */
    public function getRouteDefaults()
    {
        return $this->routeDefaults;
    }

    /**
     * Sets the route requirements.
     *
     * @param array $routeRequirements The route requirements.
     *
     * @return Action The action (fluent interface).
     */
    public function setRouteRequirements(array $routeRequirements)
    {
        $this->routeRequirements = $routeRequirements;

        return $this;
    }

    /**
     * Returns the route requirements.
     *
     * @return array The route requirements.
     */
    public function getRouteRequirements()
    {
        return $this->routeRequirements;
    }

    /**
     * Set the route (less verbose than to use all the methods).
     *
     * @param string $routeNameSuffix    The route name suffix.
     * @param string $routePatternSuffix The route pattern suffix.
     * @param array  $routeDefaults      The route defaults (optional).
     * @param array  $routeRequirements  The route requirements (optional).
     *
     * @return Action The action (fluent interface).
     */
    public function setRoute($routeNameSuffix, $routePatternSuffix, array $routeDefaults = array(), array $routeRequirements = array())
    {
        $this->setRouteNameSuffix($routeNameSuffix);
        $this->setRoutePatternSuffix($routePatternSuffix);
        $this->setRouteDefaults($routeDefaults);
        $this->setRouteRequirements($routeRequirements);

        return $this;
    }

    public function mergeOptions(array $options = array())
    {
        foreach ($options as $name => $option) {
            if (!isset($this->options[$name])) {
                throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
            }

            if (is_array($this->options[$name])) {
                $this->mergeOption($name, $option);
            } else {
                $this->setOption($name, $option);
            }
        }
    }

    public function mergeOption($name, array $value)
    {
        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        $currentValue = $this->options[$name];
        if (!is_array($currentValue)) {
            throw new \RuntimeException('The current value must to be an array to merge.');
        }

        $this->options[$name] = array_merge_recursive($currentValue, $value);
    }

    protected function setOptions(array $options)
    {
        $this->options = $options;
    }

    protected function addOption($name, $defaultValue)
    {
        $this->options[$name] = $defaultValue;

        return $this;
    }

    protected function addOptions(array $options)
    {
        foreach ($options as $name => $defaultValue) {
            $this->addOption($name, $defaultValue);
        }

        return $this;
    }

    public function setOption($name, $value)
    {
        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        $this->options[$name] = $value;
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
        return ($this->hasOption('template') && null !== $this->getOption('template')) ? $this->getOption('template') : $this->getDefaultTemplate();
    }

    public function getDataValue($data, $fieldName)
    {
        return $data->get($fieldName);
    }

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

    protected function createFormFromFields(FieldBag $fields)
    {
        $formFactory = $this->container->get('form.factory');
        $formBuilder = $formFactory->createBuilder('form', null, array(
            'data_class' => $this->getDataClass(),
        ));
        foreach ($fields as $field) {
            $type = $field->hasOption('form_type') ? $field->getOption('form_type') : null;
            $options = $field->hasOption('form_options') ? $field->getOption('form_options') : array();
            $options['label'] = $field->getLabel();
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
