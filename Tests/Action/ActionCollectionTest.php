<?php

namespace WhiteOctober\AdminBundle\Tests\Action;

use WhiteOctober\AdminBundle\Action\ActionCollection as BaseActionCollection;

class ActionCollection extends BaseActionCollection
{
    public function getDefaultOptions()
    {
        return array(
            'list' => true,
            'edit' => true,
        );
    }

    protected function configure()
    {
    }

    public function getName()
    {
        return 'my.action_name';
    }
}

class ActionCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorOptions()
    {
        $collection = new ActionCollection();
        $this->assertSame(array('list' => true, 'edit' => true), $collection->getOptions());

        $collection = new ActionCollection(array('edit' => false));
        $this->assertSame(array('list' => true, 'edit' => false), $collection->getOptions());

        try {
            $collection = new ActionCollection(array('foo' => 'bar'));
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testOptions()
    {
        $collection = new ActionCollection();
        $this->assertTrue($collection->getOption('list'));
        $this->assertTrue($collection->getOption('edit'));
        try {
            $collection->getOption('foo');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }

        $this->assertTrue($collection->hasOption('list'));
        $this->assertTrue($collection->hasOption('edit'));
        $this->assertFalse($collection->hasOption('foo'));
    }

    public function testAddHasGetAll()
    {
        $action1 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionInterface');
        $action1->expects($this->any())->method('getFullName')->will($this->returnValue('list'));

        $action2 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionInterface');
        $action2->expects($this->any())->method('getFullName')->will($this->returnValue('edit'));

        $collection = new ActionCollection();
        $this->assertSame(array(), $collection->all());
        $this->assertFalse($collection->has('list'));
        $this->assertFalse($collection->has('edit'));

        $collection->add($action1);
        $this->assertSame(array('list' => $action1), $collection->all());
        $this->assertTrue($collection->has('list'));
        $this->assertFalse($collection->has('edit'));
        $this->assertSame($action1, $collection->get('list'));
        try {
            $collection->get('edit');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }

        $collection->add($action2);
        $this->assertSame(array('list' => $action1, 'edit' => $action2), $collection->all());
        $this->assertTrue($collection->has('list'));
        $this->assertTrue($collection->has('edit'));
        $this->assertSame($action1, $collection->get('list'));
        $this->assertSame($action2, $collection->get('edit'));

        try {
            $collection->add($action1);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('LogicException', $e);
        }
    }

    public function testClone()
    {
        $action1 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionInterface');
        $action1->expects($this->any())->method('getFullName')->will($this->returnValue('list'));

        $action2 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionInterface');
        $action2->expects($this->any())->method('getFullName')->will($this->returnValue('edit'));

        $collection = new ActionCollection();
        $collection->add($action1);
        $collection->add($action2);

        $cloned = clone $collection;
        $this->assertEquals(array('list' => $action1, 'edit' => $action2), $cloned->all());
        $this->assertEquals($action1, $cloned->get('list'));
        $this->assertNotSame($action1, $cloned->get('list'));
        $this->assertEquals($action2, $cloned->get('edit'));
        $this->assertNotSame($action2, $cloned->get('edit'));
    }
}
