<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Batch;

use WhiteOctober\AdminBundle\Admin\AdminSession;

class BatchSelector
{
    CONST ALL = 1;

    protected $session;
    protected $name;

    public function __construct(AdminSession $session, $name = 'batchSelector')
    {
        $this->session = $session;
    }

    public function select($id)
    {
        $ids = $this->session->get($this->name);
        if (!is_array($ids)) {
            $ids = array();
        }
        if (!in_array($id, $ids)) {
            $ids[] = $id;
        }
        $this->session->set($this->name, $ids);
    }

    public function unselect($id)
    {
        $ids = $this->session->get($this->name, array());
        if (!is_array($ids)) {
            $ids = array();
        }
        if (false !== $key = array_search($id, $ids)) {
            unset($ids[$key]);
        }
        var_dump($ids);
        $this->session->set($this->name, $ids);
    }

    public function selectAll()
    {
        $this->session->set($this->name, self::ALL);
    }

    public function unselectAll()
    {
        $this->session->set($this->name, array());
    }

    public function getSelected()
    {
        return $this->session->get($this->name, array());
    }

    public function isAllSelected()
    {
        return self::ALL == $this->getSelected();
    }

    public function isSelected($id)
    {
        $selected = $this->getSelected();

        return is_array($selected) ? in_array((string) $id, $selected) : true;
    }
}
