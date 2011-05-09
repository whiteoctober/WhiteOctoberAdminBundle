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
        $this->add($this->getListAction());
        if ($this->getOption('create')) {
            $this->add($this->getNewAction());
            $this->add($this->getCreateAction());
        }
        if ($this->getOption('edit')) {
            $this->add($this->getEditAction());
            $this->add($this->getUpdateAction());
        }
        if ($this->getOption('delete')) {
            $this->add($this->getDeleteAction());
        }
    }

    protected function getListAction()
    {
        $class = $this->getNamespace().'\ListAction';

        return new $class();
    }

    protected function getNewAction()
    {
        $class = $this->getNamespace().'\NewAction';

        return new $class();
    }

    protected function getCreateAction()
    {
        $class = $this->getNamespace().'\CreateAction';

        return new $class();
    }

    protected function getEditAction()
    {
        $class = $this->getNamespace().'\EditAction';

        return new $class();
    }

    protected function getUpdateAction()
    {
        $class = $this->getNamespace().'\UpdateAction';

        return new $class();
    }

    protected function getDeleteAction()
    {
        $class = $this->getNamespace().'\DeleteAction';

        return new $class();
    }

    private function getNamespace()
    {
        $class = get_class($this);

        return substr($class, 0, strrpos($class, '\\'));
    }
}
