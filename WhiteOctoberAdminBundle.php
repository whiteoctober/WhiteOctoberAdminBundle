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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WhiteOctober\AdminBundle\DependencyInjection\Compiler\AddFieldGuessersPass;
use WhiteOctober\AdminBundle\DependencyInjection\Compiler\AddActionsPass;

class WhiteOctoberAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddActionsPass());
        $container->addCompilerPass(new AddFieldGuessersPass());
    }
}
