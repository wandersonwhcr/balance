<?php

namespace BalanceTest\Model\Persistence\Db;

use Balance\Model\BooleanType;
use Balance\Model\Persistence\Db\Modules;
use Balance\Mvc\Application;
use Exception;
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

        // Banco de Dados
        $db = Application::getApplication()->getServiceManager()->get('db');
        $serviceLocator->setService('db', $db);

        // Tabela de Módulos
        $tbModules = Application::getApplication()->getServiceManager()->get('Balance\Db\TableGateway\Modules');
        $serviceLocator->setService('Balance\Db\TableGateway\Modules', $tbModules);

        // Limpar Módulos
        $tbModules->delete(function () {
            // Remover Todos
        });

        // Habilitar Módulos no Banco
        $tbModules->insert([
            'identifier' => 'ModuleA',
            'enabled'    => 1,
        ]);
        $tbModules->insert([
            'identifier' => 'ModuleC',
            'enabled'    => 1,
        ]);

        // Configurações

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

    public function testSave()
    {
        // Salvar Módulos Habilitados
        $this->component->save(new Parameters([
            'modules' => [
                'ModuleA',
                'ModuleB',
                'ModuleC',
            ],
        ]));

        // Consulta de Módulos
        $result = $this->component->fetch(new Parameters(['enabled' => BooleanType::YES]));

        // Verificações
        $this->assertCount(3, $result);
        $this->assertTrue($this->component->isEnabled($this->moduleA));
        $this->assertTrue($this->component->isEnabled($this->moduleB));
        $this->assertTrue($this->component->isEnabled($this->moduleC));
    }

    public function testSaveAndEnableOneDisabled()
    {
        // Salvar Módulos Habilitados
        $this->component->save(new Parameters([
            'modules' => ['ModuleB'],
        ]));

        // Consulta de Módulos
        $result = $this->component->fetch(new Parameters(['enabled' => BooleanType::YES]));

        // Verificações
        $this->assertCount(1, $result);
        $this->assertFalse($this->component->isEnabled($this->moduleA));
        $this->assertTrue($this->component->isEnabled($this->moduleB));
        $this->assertFalse($this->component->isEnabled($this->moduleC));
    }

    public function testSaveAndDisableAllModules()
    {
        // Desabilitar Módulos
        $this->component->save(new Parameters([
            'modules' => [],
        ]));

        // Consulta
        $result = $this->component->fetch(new Parameters(['enabled' => BooleanType::YES]));

        // Verificações
        $this->assertCount(0, $result);
        $this->assertFalse($this->component->isEnabled($this->moduleA));
        $this->assertFalse($this->component->isEnabled($this->moduleB));
        $this->assertFalse($this->component->isEnabled($this->moduleC));
    }

    public function testSynchronize()
    {
        // Novo Módulo
        $moduleD = $this->getMock('Balance\Module\ModuleInterface');
        $moduleD->method('getIdentifier')
            ->will($this->returnValue('ModuleD'));
        $moduleD->method('getName')
            ->will($this->returnValue('Testing Module D'));
        $moduleD->method('getDescription')
            ->will($this->returnValue('Description of Module D'));

        // Adicionar Novo Módulo no Gerenciador
        // Remover Módulo Antigo no Gerenciador
        $manager = $this->getMockBuilder('FooBar')
            ->setMethods(['getLoadedModules'])
            ->getMock();
        $manager
            ->method('getLoadedModules')
            ->will($this->returnValue([
                'ModuleB' => $this->moduleB,
                'ModuleC' => $this->moduleC,
                'ModuleD' => $moduleD,
            ]));
        $this->component->getServiceLocator()
            ->setAllowOverride(true)
            ->setService('ModuleManager', $manager)
            ->setAllowOverride(false);

        // Sincronizar
        $result = $this->component->synchronize(true /* force */);

        // Verificações
        $this->assertSame($this->component, $result);

        // Consultar Módulos
        $result = $this->component->fetch(new Parameters());

        // Verificações
        $this->assertCount(3, $result);

        $module = $result->current();
        $this->assertEquals('ModuleB', $module['identifier']);

        $result->next();
        $module = $result->current();
        $this->assertEquals('ModuleC', $module['identifier']);

        $result->next();
        $module = $result->current();
        $this->assertEquals('ModuleD', $module['identifier']);

        $this->assertFalse($this->component->isEnabled($this->moduleB));
        $this->assertTrue($this->component->isEnabled($this->moduleC));
        $this->assertFalse($this->component->isEnabled($moduleD));
    }

    public function testSaveWithErrors()
    {
        $this->setExpectedException('Balance\Model\ModelException');

        // Sincronizar Antes por Causa do MOCK
        $this->component->synchronize();

        $tbModules = $this->getMockBuilder('FooBar')
            ->setMethods(['update'])
            ->getMock();
        $tbModules
            ->method('update')
            ->will($this->throwException(new Exception()));
        $this->component->getServiceLocator()
            ->setAllowOverride(true)
            ->setService('Balance\Db\TableGateway\Modules', $tbModules)
            ->setAllowOverride(false);

        $this->component->save(new Parameters([
            'modules' => [
                'ModuleA',
                'ModuleB',
                'ModuleC',
            ],
        ]));
    }
}
