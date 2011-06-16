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

/**
 * Field.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Field
{
    private $name;
    private $options;

    /**
     * Constructor.
     *
     * @param string $name The name.
     * @param array $options An array of options (default empty)
     *
     * @throws \InvalidArgumentException If the name is empty.
     */
    public function __construct($name, $options = array())
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('The name cannot be empty.');
        }

        $this->name = $name;
        $this->options = $options;
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
     * Sets an option.
     *
     * @param string $name  The name..
     * @param mixed  $value The value.
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * Sets the options.
     *
     * @param array $options The options.
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Returns whether an option exists or not.
     *
     * @param string $name The name.
     *
     * @return Boolean Whether an option exists or not.
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Returns an option.
     *
     * @param string $name The name.
     *
     * @return mixed The option.
     *
     * @throws \InvalidArgumentException If the option does not exist.
     */
    public function getOption($name)
    {
        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('The option "%s" does not exist.'));
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
     * Returns the label.
     *
     * The label is the label option if it exists or the name passed by ucfirst otherwise.
     *
     * @return string The label.
     */
    public function getLabel()
    {
        return $this->hasOption('label') ? $this->getOption('label') : ucfirst($this->name);
    }
}
