<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Propel\Action;

use WhiteOctober\AdminBundle\DataManager\Base\Action\DeleteAction as BaseDeleteAction;

/**
 * DeleteAction for Propel.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class DeleteAction extends BaseDeleteAction
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('propel.delete')
        ;
    }
}
