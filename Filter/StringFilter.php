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
 * StringFilter.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class StringFilter extends Filter
{
    protected function configure()
    {
        $this->setBuildFormClosure(function (FormBuilder $formBuilder) {
            $formBuilder->add('type', 'choice', array('choices' => array(
                'contains'     => 'Contains',
                'not_contains' => 'Not contains',
                'exactly'      => 'Exactly',
            )));
            $formBuilder->add('value', 'text', array('required' => false));
        });
    }
}
