<?php

namespace WhiteOctober\AdminBundle\Tests\Admin;

use WhiteOctober\AdminBundle\Admin\Admin as BaseAdmin;

class Admin extends BaseAdmin
{
    static public $dataClass;

    protected function configure()
    {
        $this
            ->setDataClass(static::$dataClass)
        ;
    }
}

class AdminTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Admin::$dataClass = 'Article';
    }

    public function testConfigure()
    {
        $admin = new Admin();

        $this->assertSame('Article', $admin->getDataClass());
        $this->assertTrue(is_string($admin->getRouteNamePrefix()));
        $this->assertTrue(is_string($admin->getRoutePatternPrefix()));
    }

    public function testDataClass()
    {
        $admin = new Admin();

        $this->assertSame($admin, $admin->setDataClass('Model\Author'));
        $this->assertSame('Model\Author', $admin->getDataClass());
    }

    public function testRouteNamePrefix()
    {
        $admin = new Admin();

        $this->assertSame($admin, $admin->setRouteNamePrefix('foo_bar'));
        $this->assertSame('foo_bar', $admin->getRouteNamePrefix());
    }

    public function testRoutePatternPrefix()
    {
        $admin = new Admin();

        $this->assertSame($admin, $admin->setRoutePatternPrefix('/foo/bar'));
        $this->assertSame('/foo/bar', $admin->getRoutePatternPrefix());
    }
    
    public function testParametersToPropagate()
    {
        $admin = new Admin();
    }
}
