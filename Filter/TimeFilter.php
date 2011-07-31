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
 * TimeFilter.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class TimeFilter extends Filter
{
    protected function configure()
    {
        $this->setBuildFormClosure(function (FormBuilder $formBuilder) {
            $formBuilder->add('from', 'time', array('required' => false));
            $formBuilder->add('to', 'time', array('required' => false));
        });
    }
}
