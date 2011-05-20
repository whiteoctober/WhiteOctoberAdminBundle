<?php

namespace WhiteOctober\AdminBundle\Tests\Action;

use WhiteOctober\AdminBundle\Action\Action as BaseAction;

class Action extends BaseAction
{
    static public $name;
    static public $routeNameSuffix;
    static public $routePatternSuffix;

    protected function configure()
    {
        $this
            ->setName(static::$name)
            ->setRouteNameSuffix(static::$routeNameSuffix)
            ->setRoutePatternSuffix(static::$routePatternSuffix)
        ;
    }

    public function executeController()
    {
    }
}

class ActionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Action::$name = 'action.test';
        Action::$routeNameSuffix = 'foo';
        Action::$routePatternSuffix = '/bar';
    }

    public function testConfigure()
    {
        $action = new Action();
        $this->assertSame('action.test', $action->getFullName());
        $this->assertSame('foo', $action->getRouteNameSuffix());
        $this->assertSame('/bar', $action->getRoutePatternSuffix());
    }

    public function testSetGetAdmin()
    {
        $admin = $this->getMock('WhiteOctober\AdminBundle\Admin\AdminInterface');

        $action = new Action();
        $action->setAdmin($admin);
        $this->assertSame($admin, $action->getAdmin());
    }

    public function testGetDataClass()
    {
        $admin = $this->getMock('WhiteOctober\AdminBundle\Admin\AdminInterface');

        $admin
            ->expects($this->once())
            ->method('getDataClass')
            ->will($this->returnValue($dataClass = 'Model\Article'))
        ;

        $action = new Action();
        $action->setAdmin($admin);
        $this->assertSame($dataClass, $action->getDataClass());
    }

    public function testNameNamespaceFullName()
    {
        $action = new Action();

        // name
        $this->assertSame($action, $action->setName('foobar'));
        $this->assertNull($action->getNamespace());
        $this->assertSame('foobar', $action->getName());
        $this->assertSame('foobar', $action->getFullName());

        // name + namespace
        $action->setName('upsfoo.bar');
        $this->assertSame('upsfoo', $action->getNamespace());
        $this->assertSame('bar', $action->getName());
        $this->assertSame('upsfoo.bar', $action->getFullName());

        // more than one dot
        $action->setName('some.more.long');
        $this->assertSame('some.more', $action->getNamespace());
        $this->assertSame('long', $action->getName());
        $this->assertSame('some.more.long', $action->getFullName());

        // empty name
        try {
            $action->setName('');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testRouteNameSuffix()
    {
        $action = new Action();

        $this->assertSame($action, $action->setRouteNameSuffix('list'));
        $this->assertSame('list', $action->getRouteNameSuffix());
    }

    public function testRoutePatternSuffix()
    {
        $action = new Action();

        // empty string to /
        $this->assertSame($action, $action->setRoutePatternSuffix(''));
        $this->assertSame('', $action->getRoutePatternSuffix());

        // normal
        $this->assertSame($action, $action->setRoutePatternSuffix('/list'));
        $this->assertSame('/list', $action->getRoutePatternSuffix());
    }

    public function testRouteDefaults()
    {
        $action = new Action();

        $routeDefaults = array('_controller' => 'foobar');
        $this->assertSame($action, $action->setRouteDefaults($routeDefaults));
        $this->assertSame($routeDefaults, $action->getRouteDefaults());
    }

    public function testRouteRequirements()
    {
        $action = new Action();

        $routeRequirements = array('_method' => 'GET');
        $this->assertSame($action, $action->setRouteRequirements($routeRequirements));
        $this->assertSame($routeRequirements, $action->getRouteRequirements());
    }

    public function testSetRoute()
    {
        $action = new Action();

        // normal
        $this->assertSame($action, $action->setRoute('edit', '/edit'));
        $this->assertSame('edit', $action->getRouteNameSuffix());
        $this->assertSame('/edit', $action->getRoutePatternSuffix());
        $this->assertSame(array(), $action->getRouteDefaults());
        $this->assertSame(array(), $action->getRouteRequirements());

        // with defaults
        $this->assertSame($action, $action->setRoute('create', '/creando', $routeDefaults = array('foo' => 'bar')));
        $this->assertSame('create', $action->getRouteNameSuffix());
        $this->assertSame('/creando', $action->getRoutePatternSuffix());
        $this->assertSame($routeDefaults, $action->getRouteDefaults());
        $this->assertSame(array(), $action->getRouteRequirements());

        // with defaults and requirements
        $routeDefaults = array('ups' => 'man');
        $routeRequirements = array('_foo' => 'barfoo');
        $this->assertSame($action, $action->setRoute('update', '/upgrade', $routeDefaults, $routeRequirements));
        $this->assertSame('update', $action->getRouteNameSuffix());
        $this->assertSame('/upgrade', $action->getRoutePatternSuffix());
        $this->assertSame($routeDefaults, $action->getRouteDefaults());
        $this->assertSame($routeRequirements, $action->getRouteRequirements());
    }
}
