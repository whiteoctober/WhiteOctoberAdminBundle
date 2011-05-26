<?php

namespace WhiteOctober\AdminBundle\Tests\Action;

use WhiteOctober\AdminBundle\Field\Field;
use WhiteOctober\AdminBundle\Field\FieldBag;

class FieldBagTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorName()
    {
        $bag = new FieldBag();
        $this->assertSame(array(), $bag->all());

        $bag = new FieldBag(array($foo = new Field('foo'), $bar = new Field('bar')));
        $this->assertSame(array('foo' => $foo, 'bar' => $bar), $bag->all());
    }

    public function testFields()
    {
        $field1 = new Field('field1');
        $field2 = new Field('field2');
        $field3 = new Field('field3');
        $field4 = new Field('field4');

        $bag = new FieldBag();

        $bag->add($field1);
        $bag->add($field2);

        $this->assertTrue($bag->has('field1'));
        $this->assertTrue($bag->has('field2'));
        $this->assertFalse($bag->has('field3'));
        $this->assertFalse($bag->has('field4'));

        $this->assertSame($field1, $bag->get('field1'));
        $this->assertSame($field2, $bag->get('field2'));
        try {
            $bag->get('field3');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }

        $bag->replace(array($field3, $field4));
        $this->assertFalse($bag->has('field1'));
        $this->assertFalse($bag->has('field2'));
        $this->assertTrue($bag->has('field3'));
        $this->assertTrue($bag->has('field4'));

        $this->assertSame(array(
            'field3' => $field3,
            'field4' => $field4,
        ), $bag->all());
    }

    public function testCount()
    {
        $bag = new FieldBag(array(new Field('foo'), new Field('bar')));
        $this->assertSame(2, $bag->count());
        $this->assertSame(2, count($bag));
    }

    public function testGetIterator()
    {
        $field1 = new Field('field1');
        $field2 = new Field('field2');

        $bag = new FieldBag(array($field1, $field2));

        $iterator = $bag->getIterator();
        $this->assertInstanceOf('ArrayIterator', $iterator);
        $this->assertSame(array(
            'field1' => $field1,
            'field2' => $field2,
        ), iterator_to_array($iterator));
    }
}
