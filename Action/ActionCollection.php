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

/**
 * ActionCollection.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class ActionCollection implements ActionCollectionInterface
{
    private $actions;
    private $options;

    /**
     * Constructor.
     *
     * @param array $options An array of options (optional).
     */
    public function __construct(array $options = array())
    {
        $this->actions = array();

        $defaultOptions = $this->getDefaultOptions();
        if ($diff = array_diff(array_keys($options), array_keys($defaultOptions))) {
            throw new \InvalidArgumentException(sprintf('The action collection "%s" does not support the following options "".', get_class($this), implode(', ', $diff)));
        }
        $this->options = array_merge($defaultOptions, $options);

        $this->configure();
    }

    /**
     * Returns the default options.
     *
     * @return array The default options.
     */
    protected function getDefaultOptions()
    {
        return array();
    }

    /**
     * Configures the action collection.
     */
    abstract protected function configure();

    /**
     * Returns whether an option exists or not.
     *
     * @param string $name The name.
     *
     * @return Boolean Whether the option exists or not.
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Returns an option value.
     *
     * @param string $name The name.
     *
     * @return mixed The option value.
     *
     * @throws \InvalidArgumentException If the option does not exist.
     */
    public function getOption($name)
    {
        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.', $name));
        }

        return $this->options[$name];
    }

    /**
     * Returns the options.
     *
     * @return array The options.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Adds an action.
     *
     * @param ActionInterface $action An action.
     *
     * @throws \LogicException If the action is already in the collection.
     */
    public function add(ActionInterface $action)
    {
        if (isset($this->actions[$action->getFullName()])) {
            throw new \LogicException(sprintf('The action "%s" already exists.', $action->getFullName()));
        }

        $this->actions[$action->getFullName()] = $action;
    }

    /**
     * Returns whether an action is in the collection by name.
     *
     * @param string $name The action name.
     *
     * @return Boolean Whether an action exists or not in the collection.
     */
    public function has($name)
    {
        return isset($this->actions[$name]);
    }

    /**
     * Returns an action by name.
     *
     * @param string $name The name.
     *
     * @return ActionInterface The action.
     *
     * @throws \InvalidArgumentInterface If the action does not exist.
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The action "%s" already exists.', $name));
        }

        return $this->actions[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->actions;
    }

    /**
     * Clone the object.
     */
    public function __clone()
    {
        $actions = array();
        foreach ($this->actions as $name => $action) {
            $actions[$name] = clone $action;
        }
        $this->actions = $actions;
    }
}
