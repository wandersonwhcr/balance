<?php

namespace Balance\View\Table;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Paginator;

class TableTest extends TestCase
{
    public function testFormEncapsulation()
    {
        $table = new Table();
        $form  = new Form();

        $result = $table->setForm($form);
        $this->assertSame($table, $result);

        $result = $table->getForm();
        $this->assertSame($form, $result);
    }

    public function testTitleEncapsulation()
    {
        $table = new Table();

        $result = $table->setTitle('Title');
        $this->assertSame($table, $result);

        $result = $table->getTitle();
        $this->assertEquals('Title', $result);
    }

    public function testColumnsEncapsulation()
    {
        $table = new Table();

        $result = $table->getColumns();
        $this->assertEquals(array(), $result);

        $result = $table->setColumn('one', array('foo' => 'bar'));
        $this->assertSame($table, $result);

        $result = $table->getColumns();
        $this->assertEquals(array('one' => array('foo' => 'bar')), $result);

        $table->setColumn('two', array('one' => 'two'));
        $result = $table->getColumns();
        $this->assertEquals(array('one' => array('foo' => 'bar'), 'two' => array('one' => 'two')), $result);
    }

    public function testElementsAsArray()
    {
        $table = new Table();

        $result = $table->getElements();
        $this->assertInstanceOf('ArrayIterator', $result);
        $this->assertEquals(array(), $result->getArrayCopy());

        $result = $table->setElements(new ArrayIterator(array(array('foo' => 'bar'), array('one' => 'two'))));
        $this->assertSame($table, $result);

        $result = $table->getElements();
        $this->assertEquals(array(array('foo' => 'bar'), array('one' => 'two')), $result->getArrayCopy());
    }

    public function testElementsAsPaginator()
    {
        $table = new Table();

        $adapter   = new Paginator\Adapter\NullFill();
        $paginator = new Paginator\Paginator($adapter);

        $result = $table->setElements($paginator);
        $this->assertSame($table, $result);

        $result = $table->getElements();
        $this->assertSame($paginator, $result);
    }

    public function testActionEncapsulation()
    {
        $table = new Table();

        $result = $table->getActions();
        $this->assertEquals(array(), $result);

        $result = $table->setAction('one', array('foo' => 'bar'));
        $this->assertEquals($table, $result);

        $result = $table->getActions();
        $this->assertEquals(array('one' => array('foo' => 'bar')), $result);

        $table->setAction('foo', array('one' => 'two'));
        $result = $table->getActions();
        $this->assertEquals(array('one' => array('foo' => 'bar'), 'foo' => array('one' => 'two')), $result);
    }

    public function testElementActionEncapsulation()
    {
        $table = new Table();

        $result = $table->getElementActions();
        $this->assertEquals(array(), $result);

        $result = $table->setElementAction('one', array('foo' => 'bar'));
        $this->assertEquals($table, $result);

        $result = $table->getElementActions();
        $this->assertEquals(array('one' => array('foo' => 'bar')), $result);

        $table->setElementAction('foo', array('one' => 'two'));
        $result = $table->getElementActions();
        $this->assertEquals(array('one' => array('foo' => 'bar'), 'foo' => array('one' => 'two')), $result);
    }

    public function testHydratorEncapsulation()
    {
        $table    = new Table();
        $hydrator = $this->getMock('Zend\Stdlib\Hydrator\HydratorInterface');

        $result = $table->setHydrator($hydrator);
        $this->assertSame($table, $result);

        $result = $table->getHydrator();
        $this->assertSame($hydrator, $result);
    }
}
