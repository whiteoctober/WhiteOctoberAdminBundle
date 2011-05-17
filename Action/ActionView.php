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

use WhiteOctober\AdminBundle\Field\Field;

class ActionView
{
    private $action;
    private $admin;

    public function __construct(Action $action)
    {
        $this->action = $action;
        $this->admin = $action->getAdmin();
    }

    public function hasOption($name)
    {
        return $this->action->hasOption($name);
    }

    public function getOption($name)
    {
        return $this->action->getOption($name);
    }

    public function getFields()
    {
        return $this->action->getFields();
    }

    public function getDataFieldValue($data, $fieldName)
    {
        return $this->admin->getDataFieldValue($data, $fieldName);
    }

    public function renderField(Field $field, $data)
    {
        $value = $this->getDataFieldValue($data, $field->getName());

        if ($field->hasOption('template')) {
            return $this->action->renderView($field->getOption('template'), array('_field' => $field, 'value' => $value));
        }

        return $value;
    }
}
