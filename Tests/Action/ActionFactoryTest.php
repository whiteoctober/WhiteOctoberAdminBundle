<?php

namespace WhiteOctober\AdminBundle\Tests\Action;

use WhiteOctober\AdminBundle\Action\ActionFactory;

class ActionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $action1 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionInterface');
        $action1->expects($this->any())->method('getFullName')->will($this->returnValue('action1'));

        $action2 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionInterface');
        $action2->expects($this->any())->method('getFullName')->will($this->returnValue('action2'));

        $action3 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionInterface');
        $action3->expects($this->any())->method('getFullName')->will($this->returnValue('action3'));

        $action4 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionInterface');
        $action4->expects($this->any())->method('getFullName')->will($this->returnValue('action4'));

        $factory = new ActionFactory();
        $factory->add($action1);
        $factory->add($action2);
        $factory->addActions(array($action3, $action4));

        $this->assertTrue($factory->has('action1'));
        $this->assertTrue($factory->has('action2'));
        $this->assertTrue($factory->has('action3'));
        $this->assertTrue($factory->has('action4'));
        $this->assertFalse($factory->has('foo'));

        $this->assertSame($action1, $factory->get('action1'));
        $this->assertSame($action2, $factory->get('action2'));
        $this->assertSame($action3, $factory->get('action3'));
        $this->assertSame($action4, $factory->get('action4'));
        try {
            $factory->get('foo');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }

        $this->assertSame(array(
            'action1' => $action1,
            'action2' => $action2,
            'action3' => $action3,
            'action4' => $action4,
        ), $factory->all());

        $factory->remove('action2');
        $this->assertTrue($factory->has('action1'));
        $this->assertFalse($factory->has('action2'));
        $this->assertTrue($factory->has('action3'));
        $this->assertTrue($factory->has('action4'));
    }
}
