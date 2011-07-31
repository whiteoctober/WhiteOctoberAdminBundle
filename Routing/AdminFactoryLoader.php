<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Routing;

use Pablodip\ModuleBundle\Routing\ModuleFactoryLoader;

/**
 * AdminFactoryLoader.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class AdminFactoryLoader extends ModuleFactoryLoader
{
    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'white_october_admin' == $type;
    }
}
