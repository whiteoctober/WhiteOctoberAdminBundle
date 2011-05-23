<?php

namespace WhiteOctober\AdminBundle\Tests\Action;

use WhiteOctober\AdminBundle\Action\ActionCollectionFactory;

class ActionCollectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $collection1 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionCollectionInterface');
        $collection1->expects($this->any())->method('getName')->will($this->returnValue('collection1'));

        $collection2 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionCollectionInterface');
        $collection2->expects($this->any())->method('getName')->will($this->returnValue('collection2'));

        $collection3 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionCollectionInterface');
        $collection3->expects($this->any())->method('getName')->will($this->returnValue('collection3'));

        $collection4 = $this->getMock('WhiteOctober\AdminBundle\Action\ActionCollectionInterface');
        $collection4->expects($this->any())->method('getName')->will($this->returnValue('collection4'));

        $factory = new ActionCollectionFactory();
        $factory->add($collection1);
        $factory->add($collection2);
        $factory->addActionCollections(array($collection3, $collection4));

        $this->assertTrue($factory->has('collection1'));
        $this->assertTrue($factory->has('collection2'));
        $this->assertTrue($factory->has('collection3'));
        $this->assertTrue($factory->has('collection4'));
        $this->assertFalse($factory->has('foo'));

        $this->assertSame($collection1, $factory->get('collection1'));
        $this->assertSame($collection2, $factory->get('collection2'));
        $this->assertSame($collection3, $factory->get('collection3'));
        $this->assertSame($collection4, $factory->get('collection4'));
        try {
            $factory->get('foo');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }

        $this->assertSame(array(
            'collection1' => $collection1,
            'collection2' => $collection2,
            'collection3' => $collection3,
            'collection4' => $collection4,
        ), $factory->all());

        $factory->remove('collection2');
        $this->assertTrue($factory->has('collection1'));
        $this->assertFalse($factory->has('collection2'));
        $this->assertTrue($factory->has('collection3'));
        $this->assertTrue($factory->has('collection4'));
    }
}
