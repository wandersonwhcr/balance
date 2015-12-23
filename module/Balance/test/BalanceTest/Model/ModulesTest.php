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

    public function testSave()
    {
        $element = $this->getComponent();

        $data = new ArrayIterator([
            ['identifier' => 'ModuleA'],
            ['identifier' => 'ModuleB'],
        ]);

        $params = new Parameters([
            'modules' => [
                'ModuleA',
                'ModuleB',
            ],
        ]);

        $persistence = $element->getServiceLocator()->get('Balance\Model\Persistence\Modules');

        $persistence
            ->method('fetch')
            ->will($this->returnValue($data));

        $persistence
            ->expects($this->atLeastOnce())
            ->method('save')
            ->with($this->equalTo($params));

        $result = $element->save($params);

        $this->assertSame($element, $result);
    }

    public function testSaveWithoutModules()
    {
        $element = $this->getComponent();

        $data = new ArrayIterator([
            ['identifier' => 'ModuleA'],
            ['identifier' => 'ModuleB'],
        ]);

        $modifiedParams = new Parameters(['modules' => []]);

        $persistence = $element->getServiceLocator()->get('Balance\Model\Persistence\Modules');

        $persistence
            ->method('fetch')
            ->will($this->returnValue($data));

        $persistence
            ->expects($this->atLeastOnce())
            ->method('save')
            ->with($this->equalTo($modifiedParams));

        $element->save(new Parameters());
    }

    public function testSaveWithUnknownModule()
    {
        $this->setExpectedException('Balance\Model\ModelException', 'Invalid Module');

        $element = $this->getComponent();

        $data = new ArrayIterator([
            ['identifier' => 'ModuleA'],
            ['identifier' => 'ModuleB'],
        ]);

        $params = new Parameters(['modules' => ['ModuleC']]);

        $persistence = $element->getServiceLocator()->get('Balance\Model\Persistence\Modules');

        $persistence
            ->method('fetch')
            ->will($this->returnValue($data));

        $element->save($params);
    }
}
