<?php

namespace BalanceTest\Model;

use Balance\Model\EntryType;
use PHPUnit_Framework_TestCase as TestCase;

class EntryTypeTest extends TestCase
{
    public function testDefinition()
    {
        $definition = (new EntryType())->getDefinition();
        $this->assertCount(2, $definition);
        $this->assertArrayHasKey(EntryType::CREDIT, $definition);
        $this->assertArrayHasKey(EntryType::DEBIT, $definition);
    }
}
