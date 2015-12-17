<?php

namespace Balance\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Router;
use Zend\ServiceManager\ServiceManager;

class HomeTest extends TestCase
{
    public function testIndex()
    {
        // Inicialização
        $mBalance       = $this->getMock('Balance\Model\Balance');
        $element        = new Home();
        $serviceLocator = new ServiceManager();
        $form           = new Form();

        // Configurar Localizador de Serviço
        $element->setServiceLocator($serviceLocator);

        // Camada de Modelo
        $serviceLocator->setService('Balance\Model\Balance', $mBalance);

        // Consulta em Camada de Modelo
        $mBalance
            ->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue([]));

        // Formulário de Perquisa
        $mBalance
            ->expects($this->once())
            ->method('getFormSearch')
            ->will($this->returnValue($form));

        // Configurar Parâmetros de Despacho
        $element->getEvent()->setRouteMatch(new Router\RouteMatch([
            'action' => 'index',
        ]));

        // Execução
        $result = $element->dispatch(new Request());

        // Verificações
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertEquals([], $result->elements);
        $this->assertSame($form, $result->form);
    }

    public function testIndexWithInvalidRequest()
    {
        // Erro Esperado
        $this->setExpectedException('Exception', 'Invalid Request');

        // Inicialização
        $mBalance       = $this->getMock('Balance\Model\Balance');
        $element        = new Home();
        $serviceLocator = new ServiceManager();

        // Configurar Localizador de Serviço
        $element->setServiceLocator($serviceLocator);

        // Camada de Modelo
        $serviceLocator->setService('Balance\Model\Balance', $mBalance);

        // Configurar Parâmetros de Despacho
        $element->getEvent()->setRouteMatch(new Router\RouteMatch([
            'action' => 'index',
        ]));

        // Execução
        $element->dispatch($this->getMock('Zend\Stdlib\RequestInterface'));
    }
}
