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

use WhiteOctober\AdminBundle\DataManager\Base\Action\EditAction as BaseEditAction;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * EditAction for Propel.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class EditAction extends BaseEditAction
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('propel.edit')
        ;
    }
}
