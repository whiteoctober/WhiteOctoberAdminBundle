<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WhiteOctober\AdminBundle\DependencyInjection\Compiler\AddAdminsPass;

/**
 * WhiteOctobAdminBundle.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class WhiteOctoberAdminBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddAdminsPass());
    }
}
