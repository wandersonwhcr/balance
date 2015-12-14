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
}
