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

class ActionFactory
{
    private $actions;

    public function __construct()
    {
        $this->actions = array();
    }

    public function set($name, $action)
    {
        if (!$action instanceof Action && !$action instanceof ActionCollection) {
            throw new \InvalidArgumentException('The action is not an instance of Action nor ActionCollection.');
        }

        $this->actions[$name] = $action;
    }

    public function add(array $actions)
    {
        foreach ($actions as $name => $action) {
            $this->set($name, $action);
        }
    }

    public function has($name)
    {
        return isset($this->actions[$name]);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The action "%s" does not exist.', $name));
        }

        return $this->actions[$name];
    }

    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The action "%s" does not exist.', $name));
        }

        unset($this->actions[$name]);
    }

    public function all()
    {
        return $this->actions;
    }
}
