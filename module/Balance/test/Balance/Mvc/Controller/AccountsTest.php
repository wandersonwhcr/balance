<?php

namespace Balance\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceManager;

class AccountsTest extends TestCase
{
    public function testOrderAction()
    {
        // Inicialização
        $controller = new Accounts();

        // Localizador de Serviços
        $serviceLocator = new ServiceManager();

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'order',
        ]));

        // Camada de Persistência
        $persistence = $this->getMock('Balance\Model\Persistence\OrderableInterface', ['order']);
        // Configuração
        $serviceLocator->setService('Balance\Model\Persistence\Accounts', $persistence);

        // Controladora
        $controller->setServiceLocator($serviceLocator);

        // Execução
        $result = $controller->dispatch(new Request());

        // Verificação
        $this->assertInstanceOf('Zend\View\Model\JsonModel', $result);
    }
}
