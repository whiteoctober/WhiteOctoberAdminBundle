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
 * ActionFactory.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ActionFactory
{
    private $actions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->actions = array();
    }

    /**
     * Adds an action.
     *
     * @param ActionInterface $action The action.
     */
    public function add(ActionInterface $action)
    {
        $this->actions[$action->getFullName()] = $action;
    }

    /**
     * Adds actions.
     *
     * @param array An array of actions.
     */
    public function addActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->add($action);
        }
    }

    /**
     * Returns whether an action exists or not.
     *
     * @param string $name The action name.
     */
    public function has($name)
    {
        return isset($this->actions[$name]);
    }

    /**
     * Returns an action.
     *
     * @param string $name The name.
     *
     * @return ActionInterface The action.
     *
     * @throws \InvalidArgumentException If the action does not exist.
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The action "%s" does not exist.', $name));
        }

        return $this->actions[$name];
    }

    /**
     * Removes an action.
     *
     * @param string $name The name.
     *
     * @throws \InvalidArgumentException If the action does not exist.
     */
    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The action "%s" does not exist.', $name));
        }

        unset($this->actions[$name]);
    }

    /**
     * Returns all the actions.
     *
     * @return array The actions.
     */
    public function all()
    {
        return $this->actions;
    }
}
