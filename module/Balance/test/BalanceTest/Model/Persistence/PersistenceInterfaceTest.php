<?php

namespace BalanceTest\Model\Persistence;

use PHPUnit_Framework_TestCase as TestCase;

class PersistenceInterfaceTest extends TestCase
{
    public function testInstanceOf()
    {
        $element = $this->getMock('Balance\Model\Persistence\PersistenceInterface');

        $this->assertInstanceOf('Zend\EventManager\EventManagerAwareInterface', $element);
    }
}
