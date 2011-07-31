<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Action;

use Pablodip\ModuleBundle\Action\ActionCollection;

/**
 * CRUDActionCollection.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class CRUDActionCollection extends ActionCollection
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('white_october_admin.crud')
            ->addOptions(array(
                'show'   => true,
                'create' => true,
                'update' => true,
                'delete' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        $actions = array($this->getListAction());
        if ($this->getOption('show')) {
            $actions[] = $this->getShowAction();
        }
        if ($this->getOption('create')) {
            $actions[] = $this->getNewAction();
            $actions[] = $this->getCreateAction();
        }
        if ($this->getOption('update')) {
            $actions[] = $this->getEditAction();
            $actions[] = $this->getUpdateAction();
        }
        if ($this->getOption('delete')) {
            $actions[] = $this->getDeleteAction();
        }

        return $actions;
    }

    abstract protected function getListAction();

    protected function getShowAction()
    {
        return new ShowAction();
    }

    protected function getNewAction()
    {
        return new NewAction();
    }

    protected function getCreateAction()
    {
        return new CreateAction();
    }

    protected function getEditAction()
    {
        return new EditAction();
    }

    protected function getUpdateAction()
    {
        return new UpdateAction();
    }

    protected function getDeleteAction()
    {
        return new DeleteAction();
    }
}
