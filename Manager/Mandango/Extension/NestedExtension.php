<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Whiteoctober\AdminBundle\Manager\Mandango\Extension;

use WhiteOctober\AdminBundle\Manager\Mandango\Admin\MandangoAdmin;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Mandango\Document\Document;
use Mandango\Query;

/**
 * NestedExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class NestedExtension
{
    private $parentClass;
    private $parentParameterName;
    private $referenceName;

    /**
     * Constructor.
     *
     * @param string $parentClass         The parent class.
     * @param string $parentParameterName The parent parameter name in the route.
     * @param string $referenceName       The reference name in the mapping.
     */
    public function __construct($parentClass, $parentParameterName, $referenceName)
    {
        $this->parentClass = $parentClass;
        $this->parentParameterName = $parentParameterName;
        $this->referenceName = $referenceName;
    }

    /**
     * Process the extension.
     *
     * @param MandangoAdmin $admin The admin.
     */
    public function process(MandangoAdmin $admin)
    {
        $parentClass = $this->parentClass;
        $parentParameterName = $this->parentParameterName;
        $referenceName = $this->referenceName;

        // propagate parent parameter
        $admin->addParameterToPropagate($parentParameterName);

        // check parent
        $admin->addControllerPreExecute(function (ContainerInterface $container) use ($admin, $parentClass, $parentParameterName) {
            $request = $container->get('request');

            $parent = $container->get('mandango')->getRepository($parentClass)
                ->findOneById($ups = $request->attributes->get($parentParameterName))
            ;
            if (!$parent) {
                throw new NotFoundHttpException();
            }

            $request->attributes->set('_parent', $parent);
        });

        // filter query
        $filterQueryCallbacks = $admin->getOption('filterQueryCallbacks');
        $filterQueryCallbacks[] = function (Query $query, MandangoAdmin $admin) use ($referenceName) {
            $parent = $admin->getContainer()->get('request')->attributes->get('_parent');
            $query->mergeCriteria(array($referenceName => $parent->getId()));
        };
        $admin->setOption('filterQueryCallbacks', $filterQueryCallbacks);

        // create data
        $createDataCallbacks = $admin->getOption('createDataCallbacks');
        $createDataCallbacks[] = function (Document $data, MandangoAdmin $admin) use ($referenceName) {
            $parent = $admin->getContainer()->get('request')->attributes->get('_parent');
            $data->set($referenceName, $parent);
        };
        $admin->setOption('createDataCallbacks', $createDataCallbacks);
    }
}
