<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('WhiteOctoberAdminBundle:Admin:index.html.twig');
    }

    public function adminsAction($_admin = null, $home_active = false)
    {
        $adminFactory = $this->get('white_october_admin.admin_factory');
        $admins = $adminFactory->getAdmins();

        return $this->render('WhiteOctoberAdminBundle:Admin:admins.html.twig', array('admins' => $admins, '_admin' => $_admin, 'home_active' => $home_active));
    }

    public function executeAction()
    {
        $adminId = $this->get('request')->get('_white_october_admin.admin');
        $actionFullName = $this->get('request')->get('_white_october_admin.action');

        $admin = $this->container->get($adminId);
        foreach ($admin->getControllerPreExecutes() as $controllerPreExecute) {
            $controllerPreExecute($this->container);
        }

        $action = $admin->getAction($actionFullName);

        return $action->executeController();
    }
}
