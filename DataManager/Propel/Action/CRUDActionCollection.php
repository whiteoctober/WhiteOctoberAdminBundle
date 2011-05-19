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

use WhiteOctober\AdminBundle\DataManager\Base\Action\CRUDActionCollection as BaseCRUDActionCollection;

/**
 * Propel CRUDActionCollection class.
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class CRUDActionCollection extends BaseCRUDActionCollection
{
    public function getName()
    {
        return 'propel.crud';
    }
}
