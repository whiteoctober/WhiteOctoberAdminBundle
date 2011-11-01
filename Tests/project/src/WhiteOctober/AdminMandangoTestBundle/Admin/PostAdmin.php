<?php

namespace WhiteOctober\AdminMandangoTestBundle\Admin;

use WhiteOctober\AdminBundle\Manager\Mandango\Admin\MandangoAdmin;

class PostAdmin extends MandangoAdmin
{
    protected function configure()
    {
        $this
            ->setDataClass('Model\WhiteOctoberAdminMandangoTestBundle\Post')
            ->setRouteNamePrefix('admin_mandango_post')
            ->setRoutePatternPrefix('/mandango/post')
            ->addFields(array(
                'author',
                'categories',
                'title' => array('simple_filter' => true, 'advanced_filter' => true, 'sortable' => true),
                'summary' => array('sortable' => true),
                'content',
                'date' => array('advanced_filter' => true, 'advanced_filter_type' => 'date_time'),
                'relevance' => array('advanced_filter' => true, 'sortable' => true),
                'isActive' => array('advanced_filter' => true),
            ))
            //->addActionOptionProcessor('list', 'dataActions', function ($value) {
            //    $value[] = 'PablodipMandangoAdminTestBundle::dataActions/comments.html.twig';
            //    return $value;
            //})
        ;
    }
}
