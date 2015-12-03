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

        // Camada de Modelo
        $serviceLocator->setService('Balance\Model\Balance', $mBalance);

        // Configurar Localizador de Serviço
        $element->setServiceLocator($serviceLocator);

        // Consulta em Camada de Modelo
        $mBalance
            ->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue(array()));

        // Formulário de Perquisa
        $mBalance
            ->expects($this->once())
            ->method('getFormSearch')
            ->will($this->returnValue($form));

        // Configurar Parâmetros de Despacho
        $element->getEvent()->setRouteMatch(new Router\RouteMatch(array(
            'action' => 'index',
        )));

        // Execução
        $result = $element->dispatch(new Request());

        // Verificações
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertEquals(array(), $result->elements);
        $this->assertSame($form, $result->form);
    }
}
