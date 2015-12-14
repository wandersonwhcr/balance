<?php

namespace Balance\Stdlib;

use PHPUnit_Framework_TestCase as TestCase;
use StdClass;

class SynchronizerTest extends TestCase
{
    public function testColumns()
    {
        $element = new Synchronizer();

        $result = $element->setColumns(array('one', 'two'));
        $this->assertSame($element, $result);

        $result = $element->getColumns();
        $this->assertEquals(array('one', 'two'), $result);
    }

    public function testColumnsWithInvalidArrayColumn()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid Column');

        $element = new Synchronizer();

        $element->setColumns(array(array()));
    }

    public function testColumnsWithInvalidObjectCOlumn()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid Column');

        $element = new Synchronizer();

        $element->setColumns(array(new StdClass()));
    }

    public function testSyncronize()
    {
        $element = new Synchronizer();

        $element->setColumns(array('id'));

        $old = array(
            array('id' => 1, 'value' => 'A'),
            array('id' => 2, 'value' => 'B'),
        );

        $new = array(
            array('id' => 2, 'value' => 'D'),
            array('id' => 3, 'value' => 'C'),
        );

        $result = $element->synchronize($old, $new);

        $this->assertArrayHasKey(Synchronizer::INSERT, $result);
        $this->assertArrayHasKey(Synchronizer::UPDATE, $result);
        $this->assertArrayHasKey(Synchronizer::DELETE, $result);

        $insert = array(
            array('id' => 3, 'value' => 'C'),
        );

        $update = array(
            array('id' => 2, 'value' => 'D'),
        );

        $delete = array(
            array('id' => 1, 'value' => 'A'),
        );

        $this->assertEquals($insert, $result[Synchronizer::INSERT]);
        $this->assertEquals($update, $result[Synchronizer::UPDATE]);
        $this->assertEquals($delete, $result[Synchronizer::DELETE]);
    }
}
