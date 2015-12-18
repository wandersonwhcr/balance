<?php

namespace BalanceTest\Stdlib;

use Balance\Stdlib\Synchronizer;
use PHPUnit_Framework_TestCase as TestCase;
use StdClass;

class SynchronizerTest extends TestCase
{
    public function testColumns()
    {
        $element = new Synchronizer();

        $result = $element->setColumns(['one', 'two']);
        $this->assertSame($element, $result);

        $result = $element->getColumns();
        $this->assertEquals(['one', 'two'], $result);
    }

    public function testColumnsWithInvalidArrayColumn()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid Column');

        $element = new Synchronizer();

        $element->setColumns([[]]);
    }

    public function testColumnsWithInvalidObjectCOlumn()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid Column');

        $element = new Synchronizer();

        $element->setColumns([new StdClass()]);
    }

    public function testSynchronize()
    {
        $element = new Synchronizer();

        $element->setColumns(['id']);

        $old = [
            ['id' => 1, 'value' => 'A'],
            ['id' => 2, 'value' => 'B'],
        ];

        $new = [
            ['id' => 2, 'value' => 'D'],
            ['id' => 3, 'value' => 'C'],
        ];

        $result = $element->synchronize($old, $new);

        $this->assertArrayHasKey(Synchronizer::INSERT, $result);
        $this->assertArrayHasKey(Synchronizer::UPDATE, $result);
        $this->assertArrayHasKey(Synchronizer::DELETE, $result);

        $insert = [
            ['id' => 3, 'value' => 'C'],
        ];

        $update = [
            ['id' => 2, 'value' => 'D'],
        ];

        $delete = [
            ['id' => 1, 'value' => 'A'],
        ];

        $this->assertEquals($insert, $result[Synchronizer::INSERT]);
        $this->assertEquals($update, $result[Synchronizer::UPDATE]);
        $this->assertEquals($delete, $result[Synchronizer::DELETE]);
    }

    public function testSynchronizeWithMultipleColumns()
    {
        $element = new Synchronizer();

        $element->setColumns(['foo_id', 'bar_id']);

        $old = [
            ['foo_id' => 1, 'bar_id' => 1, 'value' => 'A'],
            ['foo_id' => 1, 'bar_id' => 2, 'value' => 'B'],
            ['foo_id' => 2, 'bar_id' => 1, 'value' => 'C'],
            ['foo_id' => 2, 'bar_id' => 2, 'value' => 'D'],
        ];

        $new = [
            ['foo_id' => 1, 'bar_id' => 1, 'value' => 'A'],
            ['foo_id' => 1, 'bar_id' => 2, 'value' => 'C'],
            ['foo_id' => 2, 'bar_id' => 2, 'value' => 'E'],
            ['foo_id' => 2, 'bar_id' => 3, 'value' => 'B'],
        ];

        $result = $element->synchronize($old, $new);

        $this->assertArrayHasKey(Synchronizer::INSERT, $result);
        $this->assertArrayHasKey(Synchronizer::UPDATE, $result);
        $this->assertArrayHasKey(Synchronizer::DELETE, $result);

        $insert = [
            ['foo_id' => 2, 'bar_id' => 3, 'value' => 'B'],
        ];

        $update = [
            ['foo_id' => 1, 'bar_id' => 1, 'value' => 'A'],
            ['foo_id' => 1, 'bar_id' => 2, 'value' => 'C'],
            ['foo_id' => 2, 'bar_id' => 2, 'value' => 'E'],
        ];

        $delete = [
            ['foo_id' => 2, 'bar_id' => 1, 'value' => 'C'],
        ];

        $this->assertEquals($insert, $result[Synchronizer::INSERT]);
        $this->assertEquals($update, $result[Synchronizer::UPDATE]);
        $this->assertEquals($delete, $result[Synchronizer::DELETE]);
    }
}
