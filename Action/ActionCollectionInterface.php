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
 * ActionCollectionInterface.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
interface ActionCollectionInterface
{
    /**
     * Returns the canonical name.
     *
     * @return string The canonical name.
     */
    function getName();

    /**
     * Returns the actions.
     *
     * @return array The actions.
     */
    function all();
}
