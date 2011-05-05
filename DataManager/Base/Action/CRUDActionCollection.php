<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Base\Action;

use WhiteOctober\AdminBundle\Action\ActionCollection;

abstract class CRUDActionCollection extends ActionCollection
{
    
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
        
        $this->add($this->getActionFor('list'));
        if ($this->getOption('create')) {
            $this->add($this->getActionFor('new'));
            $this->add($this->getActionFor('create'));
        }
        if ($this->getOption('edit')) {
            $this->add($this->getActionFor('edit'));
            $this->add($this->getActionFor('update'));
        }
        if ($this->getOption('delete')) {
            $this->add($this->getActionFor('delete'));
        }
    }
}
