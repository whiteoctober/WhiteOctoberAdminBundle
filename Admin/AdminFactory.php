<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Admin;

use Symfony\Component\DependencyInjection\ContainerAware;

class AdminFactory extends ContainerAware
{
    private $adminIds;
    private $fieldGuesserIds;

    public function __construct(array $adminIds, array $fieldGuesserIds)
    {
        $this->adminIds = $adminIds;
        $this->fieldGuesserIds = $fieldGuesserIds;
    }

    public function getAdminIds()
    {
        return $this->adminIds;
    }

    public function getAdmins()
    {
        $admins = array();
        foreach ($this->adminIds as $adminId) {
            $admins[$adminId] = $this->container->get($adminId);
        }

        return $admins;
    }

    public function getFieldGuesserIds()
    {
        return $this->fieldGuesserIds;
    }

    public function getFieldGuesser($alias)
    {
        if (!isset($this->fieldGuesserIds[$alias])) {
            throw new \InvalidArgumentException(sprintf('The field guesser alias "%s" does not exist.', $alias));
        }

        return $this->container->get($this->fieldGuesserIds[$alias]);
    }
}
