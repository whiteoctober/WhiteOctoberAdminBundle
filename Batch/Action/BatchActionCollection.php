<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Batch\Action;

use WhiteOctober\AdminBundle\Action\ActionCollection;

class BatchActionCollection extends ActionCollection
{
    protected function configure()
    {
        $this->add(new BatchAction());
        $this->add(new SelectAction());
        $this->add(new UnselectAction());
        $this->add(new SelectAllAction());
        $this->add(new UnselectAllAction());
    }

    public function getName()
    {
        return 'batch.collection';
    }
}
