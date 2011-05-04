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

class FieldConfigurator
{
    private $fields;

    public function __construct(array $fields)
    {
        $this->fields = array();
        foreach ($fields as $field) {
            if (!$field instanceof Field) {
                throw new \InvalidArgumentException('The field is not a Field instance.');
            }
            $this->fields[$field->getName()] = $field;
        }
    }

    public function has($name)
    {
        return isset($this->fields[$name]);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The field "%s" does not exist.', $name));
        }

        return $this->fields[$name];
    }

    public function all()
    {
        return $this->fields;
    }

    public function disable($names)
    {
        if (!is_array($names)) {
            $names = array($names);
        }

        foreach ($names as $name) {
            if (!$this->has($name)) {
                throw new \InvalidArgumentException(sprintf('The field "%s" does not exist.', $name));
            }

            unset($this->fields[$name]);
        }
    }

    public function disableAllExcept(array $fieldNames)
    {
        $fields = array();
        foreach ($fieldNames as $fieldName) {
            if (!$this->has($fieldName)) {
                throw new \InvalidArgumentException(sprintf('The field "%s" does not exist.', $name));
            }

            $fields = $this->fields[$fieldName];
        }

        $this->fields = $fields;
    }
}
