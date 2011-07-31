<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Manager\Mandango\Action;

use WhiteOctober\AdminBundle\Action\CRUDActionCollection as BaseCRUDActionCollection;

/**
 * CRUDActionCollection.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class CRUDActionCollection extends BaseCRUDActionCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getListAction()
    {
        return new ListAction();
    }
}
