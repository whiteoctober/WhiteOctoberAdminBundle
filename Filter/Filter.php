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

use Symfony\Component\Form\FormBuilder;

abstract class Filter implements FilterInterface
{
    private $buildFormClosure;
    private $constraints;
    private $filterClosure;

    public function __construct()
    {
        $this->constraints = array();

        $this->configure();

        if (null === $this->buildFormClosure) {
            throw new \RuntimeException('A filter must have a build form closure.');
        }
        if (null === $this->filterClosure) {
            throw new \RuntimeException('A filter must have a filter closure.');
        }
    }

    abstract protected function configure();

    public function setBuildFormClosure(\Closure $buildFormClosure)
    {
        $this->buildFormClosure = $buildFormClosure;

        return $this;
    }

    public function getBuildFormClosure()
    {
        return $this->buildFormClosure;
    }

    public function addConstraint(Constraint $constraint)
    {
        $this->containts[] = $constraint;

        return $this;
    }

    public function setFilterClosure(\Closure $filterClosure)
    {
        $this->filterClosure = $filterClosure;

        return $this;
    }

    public function getFilterClosure()
    {
        return $this->filterClosure;
    }

    public function buildForm(FormBuilder $formBuilder)
    {
        return call_user_func($this->buildFormClosure, $formBuilder);
    }

    public function getConstraints()
    {
        return $this->constraints;
    }

    public function filter($fieldName, array $data)
    {
        return call_user_func_array($this->filterClosure, func_get_args());
    }
}
