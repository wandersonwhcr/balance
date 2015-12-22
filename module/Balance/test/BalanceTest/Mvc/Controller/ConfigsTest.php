<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Application;
use Balance\Mvc\Controller\Configs;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http;
use Zend\Mvc\Router;
use Zend\ServiceManager\ServiceManager;

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
        $result = $element->dispatch($this->getMock('Zend\Stdlib\RequestInterface'));
        // Verificações
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertEquals([], $result->elements);
    }
}
