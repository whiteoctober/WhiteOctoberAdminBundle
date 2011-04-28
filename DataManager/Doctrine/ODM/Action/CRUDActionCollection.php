<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Doctrine\ODM\Action;

use WhiteOctober\AdminBundle\Action\ActionCollection;

class CRUDActionCollection extends ActionCollection
{
    public function getName()
    {
        return 'doctrine.odm.crud';
    }

    protected function getDefaultOptions()
    {
        return array(
            'create' => true,
            'edit'   => true,
            'delete' => true,
        );
    }

    protected function configure()
    {
        $this->add(new ListAction());
        if ($this->getOption('create')) {
            $this->add(new NewAction());
            $this->add(new CreateAction());
        }
        if ($this->getOption('edit')) {
            $this->add(new EditAction());
            $this->add(new UpdateAction());
        }
        if ($this->getOption('delete')) {
            $this->add(new DeleteAction());
        }
    }
}
