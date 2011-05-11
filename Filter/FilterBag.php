<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Filter;

class FilterBag implements \Countable, \IteratorAggregate
{
    private $filters;

    public function __construct(array $filters = array())
    {
        $this->filters = array();
        $this->addFields($filters);
    }

    public function add(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    public function addFields(array $filters)
    {
        foreach ($filters as $filter) {
            $this->add($filter);
        }
    }

    public function replace(array $filters)
    {
        $this->filters = array();
        $this->addFields($filters);
    }

    public function all()
    {
        return $this->filters;
    }

    public function count()
    {
        return count($this->filters);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->filters);
    }
}
