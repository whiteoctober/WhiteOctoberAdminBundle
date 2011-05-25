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

    /**
     * Returns an action option.
     *
     * @param string $name The name.
     *
     * @return mixed The option.
     */
    public function getOption($name)
    {
        return $this->action->getOption($name);
    }

    /**
     * Returns the action fields.
     *
     * @return FieldBag The fields.
     */
    public function getFields()
    {
        return $this->action->getFields();
    }
}
