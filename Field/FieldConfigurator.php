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
 * FieldConfigurator.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class FieldConfigurator
{
    private $fields;

    /**
     * Constructor.
     *
     * @param array $fields An array of fields.
     *
     * @throws \InvalidArgumentException If a fields is not instance of Field.
     */
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

    /**
     * Returns whether a field exists or not.
     *
     * @return Boolean Whether a field exists or not.
     */
    public function has($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * Returns a field.
     *
     * @param string $name The name.
     *
     * @return Field The field.
     *
     * @throws \InvalidArgumentException If the field does not exist.
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The field "%s" does not exist.', $name));
        }

        return $this->fields[$name];
    }

    /**
     * Return the fields.
     *
     * @return array The fields.
     */
    public function all()
    {
        return $this->fields;
    }

    /**
     * Disables one or several fields.
     *
     * @param string|array $names A name or an array of names.
     *
     * @throws \InvalidArgumentException If any field does not exist.
     */
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

    /**
     * Disables all fields except one or several.
     *
     * @param string|array $names A name or an array of names.
     *
     * @throws \InvalidArgumentException If any field does not exist.
     */
    public function disableAllExcept($names)
    {
        if (!is_array($names)) {
            $names = array($names);
        }

        $fields = array();
        foreach ($names as $name) {
            if (!$this->has($name)) {
                throw new \InvalidArgumentException(sprintf('The field "%s" does not exist.', $name));
            }

            $fields[$name] = $this->fields[$name];
        }

        $this->fields = $fields;
    }
}
