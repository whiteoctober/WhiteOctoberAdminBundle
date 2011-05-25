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

    private $options;
    private $actionDependences;

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
        $this->options = array();
        $this->actionDependences = array();

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
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name)
    {
        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        return $this->options[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Adds an option.
     *
     * @param string $name         The name.
     * @param mixed  $defaultValue The default value.
     *
     * @return Action The action (fluent interface).
     *
     * @throws \LogicException If the option already exists.
     */
    public function addOption($name, $defaultValue)
    {
        if ($this->hasOption($name)) {
            throw new \LogicException(sprintf('The option "%s" already exists.', $name));
        }

        $this->options[$name] = $defaultValue;

        return $this;
    }

    /**
     * Adds options.
     *
     * @param array $options The options as an array (the name as the key and the default value as the value).
     *
     * @return Action The action (fluent interface).
     */
    public function addOptions(array $options)
    {
        foreach ($options as $name => $defaultValue) {
            $this->addOption($name, $defaultValue);
        }

        return $this;
    }

    /**
     * Sets an option.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return Action The action (fluent interface).
     *
     * @throws \InvalidArgumentException If the option does not exist.
     */
    public function setOption($name, $value)
    {
        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        $this->options[$name] = $value;

        return $this;
    }

    public function mergeOptions(array $options)
    {
        $this->options = array_merge_recursive($this->options, $options);
    }

    /**
     * Sets the action dependences.
     *
     * The action dependences are defined as an array, with the action name as key
     * and the options merged as value.
     *
     * @param array $actionDependences The action dependences.
     *
     * @return Action The action (fluent interface).
     */
    public function setActionDependences(array $actionDependences)
    {
        $this->actionDependences = $actionDependences;

        return $this;
    }

    /**
     * Returns the action dependences.
     *
     * @return array The action dependences.
     */
    public function getActionDependences()
    {
        return $this->actionDependences;
    }

    /**
     * {@inheritdoc}
     */
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
            $guessador = new FieldGuessador($this->admin->getFieldGuessers());
            foreach ($fields as $field) {
                $guessOptions = $guessador->guessOptions($dataClass, $field->getName());
                $field->setOptions(array_merge($guessOptions, $field->getOptions()));
            }

            $this->fields = new FieldBag($fields);
        }

        return $this->fields;
    }

    /**
     * Renders a view.
     *
     * Adds the "_admin" and "_action" parameters with their view objects.
     *
     * @param string $template   The template.
     * @param array  $parameters An array of parameters (optional).
     *
     * @return string The view rendered.
     */
    public function renderView($template, array $parameters = array())
    {
        $parameters['_admin'] = $this->admin->createView();
        $parameters['_action'] = $this->createView();

        return $this->container->get('templating')->render($template, $parameters);
    }

    /**
     * Renders a view a returns a response.
     *
     * Adds the "_admin" and "_action" parameters with their view objects.
     *
     * @param string   $template   The template.
     * @param array    $parameters An array of parameters (optional).
     * @param Response $response   The response (optional).
     *
     * @return Response The response.
     */
    public function render($template, array $parameters = array(), $response = null)
    {
        $parameters['_admin'] = $this->admin->createView();
        $parameters['_action'] = $this->createView();

        return $this->container->get('templating')->renderResponse($template, $parameters, $response);
    }

    /**
     * Generates an admin URL.
     *
     * @param string  $routeNameSuffix The route name suffix.
     * @param array   $parameters      An array of parameters (optional).
     * @param Boolean $absolute        Whether to generate the URL absolute.
     *
     * @return string The URL.
     */
    public function generateAdminUrl($routeNameSuffix, array $parameters = array(), $absolute = false)
    {
        return $this->admin->generateUrl($routeNameSuffix, $parameters, $absolute);
    }

    /**
     * Creates a form from a field bag.
     *
     * @param FieldBag $fields A field bag.
     *
     * @return Form A form.
     */
    protected function createFormFromFields(FieldBag $fields)
    {
        $formFactory = $this->container->get('form.factory');
        $formBuilder = $formFactory->createBuilder('form', null, array(
            'data_class' => $this->getDataClass(),
        ));
        foreach ($fields as $field) {
            $type = $field->hasOption('formType') ? $field->getOption('formType') : null;
            $options = $field->hasOption('formOptions') ? $field->getOption('formOptions') : array();
            $options['label'] = $field->getLabel();
            $formBuilder->add($field->getName(), $type, $options);
        }
        $form = $formBuilder->getForm();

        return $form;
    }

    /**
     * Returns whether a container service exists.
     *
     * @return Boolean Whether a container service exists.
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Returns a container service.
     *
     * @param string $id The service id.
     *
     * @return mixed The container service.
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Returns an action view instance with the action.
     *
     * @return ActionView An action view instance with the action.
     */
    public function createView()
    {
        return new ActionView($this);
    }
}
