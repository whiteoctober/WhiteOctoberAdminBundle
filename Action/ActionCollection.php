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

abstract class ActionCollection
{
    private $actions;
    private $options;

    public function __construct(array $options = array())
    {
        $this->options = array_merge($this->getDefaultOptions(), $options);

        $this->configure();

        if (isset($this->options['_actions'])) {
            foreach ($this->options['_actions'] as $name => $options) {
                if (!isset($this->actions[$name])) {
                    throw new \RuntimeException(sprintf('The action "%s" does not exist.', $name));
                }

                $this->actions[$name]->mergeOptions($options);
            }
        }
    }

    protected function getDefaultOptions()
    {
        return array();
    }

    abstract protected function getName();

    abstract protected function configure();

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

    protected function add(Action $action)
    {
        if (isset($this->actions[$action->getName()])) {
            throw new \InvalidArgumentException(sprintf('The action "%s" already exists.', $action->getName()));
        }

        $this->actions[$action->getName()] = $action;
    }

    public function all()
    {
        return $this->actions;
    }

    public function __clone()
    {
        $actions = array();
        foreach ($this->actions as $name => $action) {
            $actions[$name] = clone $action;
        }
        $this->actions = $actions;
    }
}
