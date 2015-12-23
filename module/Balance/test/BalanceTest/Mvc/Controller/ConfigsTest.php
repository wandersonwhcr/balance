<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Model\ModelException;
use Balance\Mvc\Application;
use Balance\Mvc\Controller\Configs;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http;
use Zend\Mvc\Router;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class ConfigsTest extends TestCase
{
    public function testIndex()
    {
        // Inicialização
        $element = new Configs();
        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        $element->setServiceLocator($serviceLocator);
        // Configurar Parâmetros de Despacho
        $element->getEvent()->setRouteMatch(new Router\RouteMatch([
            'action' => 'js',
        ]));
        // Execução
        $result = $element->dispatch(new Http\PhpEnvironment\Request());
        // Verificações
        $this->assertInstanceOf('Zend\View\Model\JsonModel', $result);
        $this->assertRegexp('/^\$.application.setConfigs/', $result->serialize());
    }

    public function testIndexWithInvalidRequest()
    {
        // Exceções Esperadas
        $this->setExpectedException('Exception', 'Invalid Request');
        // Inicialização
        $element = new Configs();
        // Configurar Parâmetros de Despacho
        $element->getEvent()->setRouteMatch(new Router\RouteMatch([
            'action' => 'js',
        ]));
        // Execução
        $element->dispatch($this->getMock('Zend\Stdlib\RequestInterface'));
    }

    public function testModules()
    {
        // Inicialização
        $element = new Configs();
        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        $element->setServiceLocator($serviceLocator);
        // Camada de Modelo
        $mModules = $this->getMock('Balance\Model\Modules');
        // Método: Consultar Módulos
        $mModules->method('fetch')->will($this->returnValue([]));
        // Serviço
        $serviceLocator->setService('Balance\Model\Modules', $mModules);
        // Configurar Parâmetros de Despacho
        $element->getEvent()->setRouteMatch(new Router\RouteMatch([
            'action' => 'modules',
        ]));
        // Execução
        $result = $element->dispatch(new Http\PhpEnvironment\Request());
        // Verificações
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertEquals([], $result->elements);
    }

    public function testModulesAndSave()
    {
        // Inicialização
        $element = new Configs();
        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        $element->setServiceLocator($serviceLocator);
        // Camada de Modelo
        $mModules = $this->getMock('Balance\Model\Modules');
        // Dados de Salvamento
        $data = new Parameters(['modules' => ['ModuleA', 'ModuleB']]);
        // Salvar com Dados Corretos
        $mModules->expects($this->once())
            ->method('save')
            ->with($this->equalTo($data))
            ->will($this->returnSelf());
        // Serviço
        $serviceLocator->setService('Balance\Model\Modules', $mModules);
        // Configurar Parâmetros de Despacho
        $element->getEvent()->setRouteMatch(new Router\RouteMatch([
            'action' => 'modules',
        ]));
        // Requisição
        $request = (new Http\PhpEnvironment\Request())
            ->setMethod('POST')
            ->setPost($data);
        // Execução
        $result = $element->dispatch($request);
        // Verificações
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
    }

    public function testModulesAndSaveWithErrors()
    {
        // Inicialização
        $element = new Configs();
        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        $element->setServiceLocator($serviceLocator);
        // Camada de Modelo
        $mModules = $this->getMock('Balance\Model\Modules');
        // Salvar com Dados Corretos
        $mModules->expects($this->once())
            ->method('save')
            ->will($this->throwException(new ModelException()));
        // Serviço
        $serviceLocator->setService('Balance\Model\Modules', $mModules);
        // Configurar Parâmetros de Despacho
        $element->getEvent()->setRouteMatch(new Router\RouteMatch([
            'action' => 'modules',
        ]));
        // Requisição
        $request = (new Http\PhpEnvironment\Request())->setMethod('POST');
        // Execução
        $element->dispatch($request);
    }
}
