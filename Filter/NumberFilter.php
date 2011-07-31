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

/**
 * NumberFilter.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class NumberFilter extends Filter
{
    protected function configure()
    {
        $this->setBuildFormClosure(function (FormBuilder $formBuilder) {
            $formBuilder->add('type', 'choice', array('choices' => array(
                'exactly'                  => 'Exactly',
                'greater_than'             => 'Greater than',
                'greater_than_or_equal_to' => 'Greater than or Equal to',
                'less_than'                => 'Less than',
                'less_than_or_equal_to'    => 'Less than or Equal to',
            )));
            $formBuilder->add('value', 'text', array('required' => false));
        });
    }
}
