<?php

namespace Balance\Stdlib;

use PHPUnit_Framework_TestCase as TestCase;

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
}
