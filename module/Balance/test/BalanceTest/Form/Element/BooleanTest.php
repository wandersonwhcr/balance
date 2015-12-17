<?php

namespace BalanceTest\Form\Element;

use Balance\Form\Element\Boolean;
use Balance\Model\BooleanType;
use PHPUnit_Framework_TestCase as TestCase;

class BooleanTest extends TestCase
{
    public function testValueOptions()
    {
        $element = new Boolean();

        $values = $element->getValueOptions();

        $this->assertCount(2, $values);
        $this->assertEquals([
            BooleanType::YES => 'Sim',
            BooleanType::NO  => 'Não',
        ], $values);
    }
}
