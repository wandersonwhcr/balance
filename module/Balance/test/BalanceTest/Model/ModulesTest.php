<?php

namespace BalanceTest\Model;

use ArrayIterator;
use Balance\Model\Modules;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class ModulesTest extends TestCase
{
    protected function getComponent()
    {
        $element = new Modules();

        $serviceLocator = new ServiceManager();
        $element->setServiceLocator($serviceLocator);

        $pModules = $this->getMock('Balance\Model\Persistence\PersistenceInterface');
        $serviceLocator->setService('Balance\Model\Persistence\Modules', $pModules);

        return $element;
    }

    public function testFetch()
    {
        $element = $this->getComponent();

        $data = new ArrayIterator();

        $element->getServiceLocator()->get('Balance\Model\Persistence\Modules')
            ->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($data));

        $result = $element->fetch(new Parameters());

        $this->assertSame($data, $result);
    }

    public function testFetchWithoutTraversable()
    {
        $this->setExpectedException('Balance\Model\ModelException', 'Persistence Result is not Traversable');

        $this->getComponent()->fetch(new Parameters());
    }
}
