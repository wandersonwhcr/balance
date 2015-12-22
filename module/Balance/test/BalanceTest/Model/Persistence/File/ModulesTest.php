<?php

namespace BalanceTest\Model\Persistence\File;

use Balance\Model\Persistence\File\Modules;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;

class ModulesTest extends TestCase
{
    public function testIsEnabled()
    {
        $component = new Modules();

        $serviceLocator = new ServiceManager();
        $component->setServiceLocator($serviceLocator);

        $moduleA = $this->getMock('Balance\Module\ModuleInterface');
        $moduleA
            ->method('getIdentifier')
            ->will($this->returnValue('ModuleA'));

        $moduleB = $this->getMock('Balance\Module\ModuleInterface');
        $moduleB
            ->method('getIdentifier')
            ->will($this->returnValue('ModuleB'));

        $moduleC = $this->getMock('Balance\Module\ModuleInterface');
        $moduleC
            ->method('getIdentifier')
            ->will($this->returnValue('ModuleC'));

        $serviceLocator->setService('Config', [
            'balance_modules' => [
                'ModuleA',
                'ModuleC',
            ],
        ]);

        $this->assertTrue($component->isEnabled($moduleA));
        $this->assertFalse($component->isEnabled($moduleB));
        $this->assertTrue($component->isEnabled($moduleC));
    }
}
