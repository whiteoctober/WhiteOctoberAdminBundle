<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Admin;

use Pablodip\ModuleBundle\Module\ModuleView;
use Pablodip\ModuleBundle\Field\Field;

/**
 * AdminView.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class AdminView extends ModuleView
{
    /**
     * Constructor.
     *
     * @param Admin $admin The admin.
     */
    public function __construct(Admin $admin)
    {
        parent::__construct($admin);
    }

    /**
     * Renders a field for a data.
     *
     * It render the template option of the field if exists, or return the value otherwise.
     *
     * @param mixed $data  The data.
     * @param Field $field A field.
     */
    public function renderDataField($data, Field $field)
    {
        if ($field->hasOption('template')) {
            return $this->module->getContainer()->get('templating')->render($field->getOption('template'), array(
                '_module' => $this->module,
                'data'    => $data,
                'field'   => $field,
            ));
        }

        return $this->getDataFieldValue($data, $field->getName());
    }
}
