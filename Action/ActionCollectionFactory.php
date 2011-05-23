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

/**
 * ActionCollectionFactory.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class ActionCollectionFactory
{
    private $actionCollections;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->actionCollections = array();
    }

    /**
     * Adds an action collection.
     *
     * @param ActionInterface $action The action.
     */
    public function add(ActionCollectionInterface $actionCollection)
    {
        $this->actionCollections[$actionCollection->getName()] = $actionCollection;
    }

    /**
     * Adds action collections.
     *
     * @param array An array of action collections.
     */
    public function addActionCollections(array $actionCollections)
    {
        foreach ($actionCollections as $actionCollection) {
            $this->add($actionCollection);
        }
    }

    /**
     * Returns whether an action collection exists or not.
     *
     * @param string $name The action collection name.
     */
    public function has($name)
    {
        return isset($this->actionCollections[$name]);
    }

    /**
     * Returns an action collection.
     *
     * @param string $name The name.
     *
     * @return ActionCollectionInterface The action collection.
     *
     * @throws \InvalidArgumentException If the action collection does not exist.
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The action collection "%s" does not exist.', $name));
        }

        return $this->actionCollections[$name];
    }

    /**
     * Removes an action collection.
     *
     * @param string $name The name.
     *
     * @throws \InvalidArgumentException If the action collection does not exist.
     */
    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('The action collection "%s" does not exist.', $name));
        }

        unset($this->actionCollections[$name]);
    }

    /**
     * Returns all the action collections.
     *
     * @return array The action collections.
     */
    public function all()
    {
        return $this->actionCollections;
    }
}
