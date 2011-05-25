<?php

namespace WhiteOctober\AdminBundle\Tests\Action;

use WhiteOctober\AdminBundle\Action\ActionView;

class ActionViewTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOption()
    {
        $action = $this->getMock('WhiteOctober\AdminBundle\Action\ActionInterface');

        $action
            ->expects($this->once())
            ->method('getOption')
            ->with('foo')
            ->will($this->returnValue('bar'))
        ;

        $view = new ActionView($action);
        $this->assertSame('bar', $view->getOption('foo'));
    }

    public function testGetFields()
    {
        $action = $this->getMock('WhiteOctober\AdminBundle\Action\ActionInterface');

        $action
            ->expects($this->once())
            ->method('getFields')
            ->will($this->returnValue($fields = new \DateTime()))
        ;

        $view = new ActionView($action);
        $this->assertSame($fields, $view->getFields());
    }
}
