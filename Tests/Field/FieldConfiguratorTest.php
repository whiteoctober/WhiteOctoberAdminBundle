<?php

namespace WhiteOctober\AdminBundle\Tests\Action;

use WhiteOctober\AdminBundle\Field\Field;
use WhiteOctober\AdminBundle\Field\FieldConfigurator;

class FieldConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorHasGetAll()
    {
        $field1 = new Field('field1');
        $field2 = new Field('field2');

        $configurator = new FieldConfigurator(array($field1, $field2));
        try {
            new FieldConfigurator(array(new Field('foo'), new \DateTime(), new Field('bar')));
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }

        $this->assertTrue($configurator->has('field1'));
        $this->assertTrue($configurator->has('field2'));
        $this->assertFalse($configurator->has('field3'));

        $this->assertSame($field1, $configurator->get('field1'));
        $this->assertSame($field2, $configurator->get('field2'));
        try {
            $configurator->get('field3');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }

        $this->assertSame(array(
            'field1' => $field1,
            'field2' => $field2,
        ), $configurator->all());
    }

    public function testDisable()
    {
        $field1 = new Field('field1');
        $field2 = new Field('field2');
        $field3 = new Field('field3');
        $field4 = new Field('field4');

        $configurator = new FieldConfigurator(array($field1, $field2, $field3, $field4));

        $configurator->disable('field2');
        $this->assertSame(array('field1', 'field3', 'field4'), array_keys($configurator->all()));

        $configurator->disable(array('field1', 'field3'));
        $this->assertSame(array('field4'), array_keys($configurator->all()));

        try {
            $configurator->disable('field2');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testDisableAllExcept()
    {
        $field1 = new Field('field1');
        $field2 = new Field('field2');
        $field3 = new Field('field3');
        $field4 = new Field('field4');
        $field5 = new Field('field5');
        $field6 = new Field('field6');

        $configurator = new FieldConfigurator(array($field1, $field2, $field3, $field4, $field5, $field6));

        $configurator->disableAllExcept(array('field2', 'field3', 'field5', 'field6'));
        $this->assertSame(array('field2', 'field3', 'field5', 'field6'), array_keys($configurator->all()));

        $configurator->disableAllExcept('field3');
        $this->assertSame(array('field3'), array_keys($configurator->all()));

        try {
            $configurator->disableAllExcept('field2');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }
}
