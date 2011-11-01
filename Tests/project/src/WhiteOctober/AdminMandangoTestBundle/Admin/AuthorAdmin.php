<?php

namespace WhiteOctober\AdminMandangoTestBundle\Admin;

use WhiteOctober\AdminBundle\Manager\Mandango\Admin\MandangoAdmin;

class AuthorAdmin extends MandangoAdmin
{
    protected function configure()
    {
        $this
            ->setDataClass('Model\WhiteOctoberAdminMandangoTestBundle\Author')
            ->setRouteNamePrefix('admin_mandango_author')
            ->setRoutePatternPrefix('/mandango/author')
            ->addFields(array(
                'name' => array('simple_filter' => true),
            ))
        ;
    }
}
