<?php

namespace BalanceTest\View\Table;

use ArrayIterator;
use Balance\View\Table\Table;
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
        $this->assertEquals([], $result);

        $result = $table->setColumn('one', ['foo' => 'bar']);
        $this->assertSame($table, $result);

        $result = $table->getColumns();
        $this->assertEquals(['one' => ['foo' => 'bar']], $result);

        $table->setColumn('two', ['one' => 'two']);
        $result = $table->getColumns();
        $this->assertEquals(['one' => ['foo' => 'bar'], 'two' => ['one' => 'two']], $result);
    }

    public function testElementsAsArray()
    {
        $table = new Table();

        $result = $table->getElements();
        $this->assertInstanceOf('ArrayIterator', $result);
        $this->assertEquals([], $result->getArrayCopy());

        $result = $table->setElements(new ArrayIterator([['foo' => 'bar'], ['one' => 'two']]));
        $this->assertSame($table, $result);

        $result = $table->getElements();
        $this->assertEquals([['foo' => 'bar'], ['one' => 'two']], $result->getArrayCopy());
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
        $this->assertEquals([], $result);

        $result = $table->setAction('one', ['foo' => 'bar']);
        $this->assertEquals($table, $result);

        $result = $table->getActions();
        $this->assertEquals(['one' => ['foo' => 'bar']], $result);

        $table->setAction('foo', ['one' => 'two']);
        $result = $table->getActions();
        $this->assertEquals(['one' => ['foo' => 'bar'], 'foo' => ['one' => 'two']], $result);
    }

    public function testElementActionEncapsulation()
    {
        $table = new Table();

        $result = $table->getElementActions();
        $this->assertEquals([], $result);

        $result = $table->setElementAction('one', ['foo' => 'bar']);
        $this->assertEquals($table, $result);

        $result = $table->getElementActions();
        $this->assertEquals(['one' => ['foo' => 'bar']], $result);

        $table->setElementAction('foo', ['one' => 'two']);
        $result = $table->getElementActions();
        $this->assertEquals(['one' => ['foo' => 'bar'], 'foo' => ['one' => 'two']], $result);
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
