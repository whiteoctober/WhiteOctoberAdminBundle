<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Field;

class Field
{
    private $name;
    private $options;

    public function __construct($name, array $options = array())
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('The name cannot be empty.');
        }

        $this->name = $name;
        $this->options = $options;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function appendOptions(array $options)
    {
        $this->options = array_merge($options, $this->options);
    }

    public function mergeOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
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
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.'));
        }

        return $this->options[$name];
    }

    public function getLabel()
    {
        return $this->hasOption('label') ? $this->getOption('label') : ucfirst($this->name);
    }

    public function getType()
    {
        return $this->hasOption('type') ? $this->getOption('type') : null;
    }

    public function getTemplate()
    {
        return $this->hasOption('template') ? $this->getOption('template') : null;
    }
}
