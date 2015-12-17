<?php

namespace BalanceTest\Model;

use Balance\Model\BooleanType;
use PHPUnit_Framework_TestCase as TestCase;

class BooleanTypeTest extends TestCase
{
    public function testDefinition()
    {
        $definition = (new BooleanType())->getDefinition();
        $this->assertCount(2, $definition);
        $this->assertArrayHasKey(BooleanType::YES, $definition);
        $this->assertArrayHasKey(BooleanType::NO, $definition);
    }
}
