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

interface ActionInterface
{
    /**
     * Returns the namespace.
     *
     * @return string The namespace.
     */
    function getNamespace();

    /**
     * Returns the name.
     *
     * @return string The name.
     */
    function getName();

    /**
     * Returns the full name (namespace + name).
     *
     * @return string The full name.
     */
    function getFullName();

    /**
     * Returns the route name suffix.
     *
     * @return string The route name suffix.
     */
    function getRouteNameSuffix();

    /**
     * Returns the route pattern suffix.
     *
     * @return string The route pattern suffix.
     */
    function getRoutePatternSuffix();

    /**
     * Returns the route defaults.
     *
     * @return array The route defaults.
     */
    function getRouteDefaults();

    /**
     * Returns the route requirements.
     *
     * @return array The route requirements.
     */
    function getRouteRequirements();

    /**
     * Executes the controller.
     *
     * @return Response A response object.
     */
    function executeController();
}
