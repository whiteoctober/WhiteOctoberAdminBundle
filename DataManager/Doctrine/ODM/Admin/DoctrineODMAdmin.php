<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Doctrine\ODM\Admin;

use WhiteOctober\AdminBundle\Admin\Admin;

abstract class DoctrineODMAdmin extends Admin
{
    protected function preConfigure()
    {
        parent::preConfigure();

        $this->addFieldGuesser('doctrine.odm');
    }
}
