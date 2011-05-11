<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Filter;

use Symfony\Component\Form\FormBuilder;

abstract class BooleanFilter extends Filter
{
    protected function configure()
    {
        $this->setBuildFormClosure(function (FormBuilder $formBuilder) {
            $formBuilder->add('value', 'choice', array('choices' => array(
                'yes_or_no' => 'Yes or No',
                'yes'       => 'Yes',
                'no'        => 'No',
            )));
        });
    }
}
