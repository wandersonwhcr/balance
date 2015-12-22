<?php

namespace BalanceTest\Model\Persistence\File;

use Balance\Model\BooleanType;
use Balance\Model\Persistence\File\Modules;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class ModulesTest extends TestCase
{
    private $component;

    private $moduleA;
    private $moduleB;
    private $moduleC;

    protected function setUp()
    {
        $component = new Modules();

        $serviceLocator = new ServiceManager();
        $component->setServiceLocator($serviceLocator);

        $moduleA = $this->getMock('Balance\Module\ModuleInterface');
        $moduleA->method('getIdentifier')
            ->will($this->returnValue('ModuleA'));
        $moduleA->method('getName')
            ->will($this->returnValue('Testing Module A'));
        $moduleA->method('getDescription')
            ->will($this->returnValue('Description of Module A'));

        $moduleB = $this->getMock('Balance\Module\ModuleInterface');
        $moduleB->method('getIdentifier')
            ->will($this->returnValue('ModuleB'));
        $moduleB->method('getName')
            ->will($this->returnValue('Testing Module B'));
        $moduleB->method('getDescription')
            ->will($this->returnValue('Description of Module B'));

        $moduleC = $this->getMock('Balance\Module\ModuleInterface');
        $moduleC
            ->method('getIdentifier')
            ->will($this->returnValue('ModuleC'));
        $moduleC->method('getName')
            ->will($this->returnValue('Testing Module C'));
        $moduleC->method('getDescription')
            ->will($this->returnValue('Description of Module C'));

        $manager = $this->getMockBuilder('FooBar')
            ->setMethods(['getLoadedModules'])
            ->getMock();

        $manager
            ->method('getLoadedModules')
            ->will($this->returnValue([
                'ModuleA' => $moduleA,
                'ModuleB' => $moduleB,
                'ModuleC' => $moduleC,
            ]));

        $serviceLocator->setService('ModuleManager', $manager);

        $serviceLocator->setService('Config', [
            'balance_modules' => [
                'ModuleA',
                'ModuleC',
            ],
        ]);

        $this->component = $component;

        $this->moduleA = $moduleA;
        $this->moduleB = $moduleB;
        $this->moduleC = $moduleC;
    }

    protected function tearDown()
    {
        unset($this->component);

        unset($this->moduleA);
        unset($this->moduleB);
        unset($this->moduleC);
    }

    public function testFetch()
    {
        $result = $this->component->fetch(new Parameters());

        $this->assertInstanceOf('Traversable', $result);
        $this->assertCount(3, $result);

        $module = $result->current();
        $this->assertEquals('ModuleA', $module['identifier']);
        $this->assertEquals('Testing Module A', $module['name']);
        $this->assertEquals('Description of Module A', $module['description']);
        $this->assertTrue($module['enabled']);

        $result->next();
        $module = $result->current();
        $this->assertEquals('ModuleB', $module['identifier']);
        $this->assertEquals('Testing Module B', $module['name']);
        $this->assertEquals('Description of Module B', $module['description']);
        $this->assertFalse($module['enabled']);

        $result->next();
        $module = $result->current();
        $this->assertEquals('ModuleC', $module['identifier']);
        $this->assertEquals('Testing Module C', $module['name']);
        $this->assertEquals('Description of Module C', $module['description']);
        $this->assertTrue($module['enabled']);
    }

    public function testFetchEnabled()
    {
        $result = $this->component->fetch(new Parameters(['enabled' => BooleanType::YES]));

        $this->assertCount(2, $result);

        $module = $result->current();
        $this->assertEquals('ModuleA', $module['identifier']);

        $result->next();
        $module = $result->current();
        $this->assertEquals('ModuleC', $module['identifier']);
    }

    public function testFetchDisabled()
    {
        $result = $this->component->fetch(new Parameters(['enabled' => BooleanType::NO]));

        $this->assertCount(1, $result);

        $module = $result->current();
        $this->assertEquals('ModuleB', $module['identifier']);
    }

    public function testIsEnabled()
    {
        $this->assertTrue($this->component->isEnabled($this->moduleA));
        $this->assertFalse($this->component->isEnabled($this->moduleB));
        $this->assertTrue($this->component->isEnabled($this->moduleC));
    }
}
