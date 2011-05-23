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

/**
 * ActionView.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ActionView
{
    private $action;

    /**
     * Constructor.
     *
     * @param ActionInterface $action An action.
     */
    public function __construct(ActionInterface $action)
    {
        $this->action = $action;
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
        return $this->action->getAdmin()->getDataFieldValue($data, $fieldName);
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
