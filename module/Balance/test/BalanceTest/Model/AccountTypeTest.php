<?php

namespace BalanceTest\Model;

use Balance\Model\AccountType;
use PHPUnit_Framework_TestCase as TestCase;

class AccountTypeTest extends TestCase
{
    public function testDefinition()
    {
        $definition = (new AccountType())->getDefinition();
        $this->assertCount(2, $definition);
        $this->assertArrayHasKey(AccountType::ACTIVE, $definition);
        $this->assertArrayHasKey(AccountType::PASSIVE, $definition);
    }
}
