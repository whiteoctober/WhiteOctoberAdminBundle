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

class FieldBag implements \Countable, \IteratorAggregate
{
    private $fields;

    public function __construct(array $fields = array())
    {
        $this->fields = array();
        $this->addFields($fields);
    }

    public function add(Field $field)
    {
        $this->fields[$field->getName()] = $field;
    }

    public function addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->add($field);
        }
    }

    public function replace(array $fields)
    {
        $this->fields = array();
        $this->addFields($fields);
    }

    public function has($name)
    {
        return isset($this->fields[$name]);
    }

    public function get($name)
    {
        if (!isset($this->fields[$name])) {
            throw new \InvalidArgumentException(sprintf('The field "%s" does not exist.'));
        }

        return $this->fields[$name];
    }

    public function all()
    {
        return $this->fields;
    }

    public function count()
    {
        return count($this->fields);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }
}
