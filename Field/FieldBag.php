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
 * FieldBag.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class FieldBag implements \Countable, \IteratorAggregate
{
    private $fields;

    /**
     * Constructor.
     *
     * @param array $fields An array of fields (optional).
     */
    public function __construct(array $fields = array())
    {
        $this->fields = array();
        $this->addFields($fields);
    }

    /**
     * Adds a field.
     *
     * @param Field $field A field.
     */
    public function add(Field $field)
    {
        $this->fields[$field->getName()] = $field;
    }

    /**
     * Adds an array of fields.
     *
     * @param array $fields An array of fields.
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->add($field);
        }
    }

    /**
     * Returns whether a field exists or not.
     *
     * @param string $name The field name.
     *
     * @return Boolean Whether the field exists or not.
     */
    public function has($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * Returns a field.
     *
     * @param string $name The field name.
     *
     * @return Field The field.
     *
     * @throws \InvalidArgumentException If the field does not exist.
     */
    public function get($name)
    {
        if (!isset($this->fields[$name])) {
            throw new \InvalidArgumentException(sprintf('The field "%s" does not exist.', $name));
        }

        return $this->fields[$name];
    }

    /**
     * Replaces the fields.
     *
     * @param array $field An array of fields.
     */
    public function replace(array $fields)
    {
        $this->fields = array();
        $this->addFields($fields);
    }

    /**
     * Returns the fields.
     *
     * @return array An array of fields.
     */
    public function all()
    {
        return $this->fields;
    }

    /**
     * Returns the number of fields (implements the \Countable interface).
     *
     * @return integer The number of fields.
     */
    public function count()
    {
        return count($this->fields);
    }

    /**
     * Implements the \IteratorAggregate interface.
     *
     * @return \ArrayIterator An \ArrayIterator with the fields.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }
}
