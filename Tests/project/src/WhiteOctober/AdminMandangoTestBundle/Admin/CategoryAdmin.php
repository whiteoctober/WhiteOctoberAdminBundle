<?php

namespace WhiteOctober\AdminMandangoTestBundle\Admin;

use WhiteOctober\AdminBundle\Manager\Mandango\Admin\MandangoAdmin;

class CategoryAdmin extends MandangoAdmin
{
    protected function configure()
    {
        $this
            ->setDataClass('Model\WhiteOctoberAdminMandangoTestBundle\Category')
            ->setRouteNamePrefix('admin_mandango_category')
            ->setRoutePatternPrefix('/mandango/category')
            ->addFields(array(
                'name',
            ))
        ;
    }
}
