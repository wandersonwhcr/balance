<?php

namespace Balance\Mvc\Controller;

use Balance\Model\ModelException;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceManager;

class RemoveActionController
    extends AbstractActionController
    implements ModelAwareInterface, RedirectRouteNameAwareInterface
{
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
    use RemoveActionTrait;
}

class RemoveActionTraitTest extends TestCase
{
    protected function getController($type)
    {
        // Localizador de Serviços
        $serviceLocator = new ServiceManager();

        // Template
        $templateListener = $this->getMock('Zend\Mvc\View\Http\InjectTemplateListener');
        // Gerenciador de Visualização
        $viewManager = $this->getMock('Zend\Mvc\View\Http\ViewManager');
        // Capturar o Injetor
        $viewManager
            ->method('getInjectTemplateListener')
            ->will($this->returnValue($templateListener));
        // Configuração
        $serviceLocator->setService('ViewManager', $viewManager);

        // Camada de Modelo
        $model = $this->getMockBuilder('Balance\Model\Model')
            ->disableOriginalConstructor()
            ->getMock();

        // Pesquisa
        $model
            ->method('fetch')
            ->will($this->returnValue(array(array('one' => 'two'))));

        // Controladora
        switch ($type) {
            case 'remove-action-controller':
                $controller = new RemoveActionController();
                break;
        }

        // Configurações
        $controller
            ->setModel($model)
            ->setServiceLocator($serviceLocator);

        // Plugin de Redirecionamento
        $redirect = $this->getMock('Zend\Mvc\Controller\Plugin\Redirect');
        // Configurações
        $controller->getPluginManager()->setService('redirect', $redirect);

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch(array(
            'action' => 'remove',
        )));

        // Apresentação
        return $controller;
    }

    public function testRemoveAction()
    {
        // Inicialização
        $controller = $this->getController('remove-action-controller');

        // Execução
        $controller->dispatch(new Request());
    }

    public function testRemoveActionAndException()
    {
        // Inicialização
        $controller = $this->getController('remove-action-controller');

        // Camada de Modelo
        $model = $controller->getModel()
            ->method('remove')
            ->will($this->throwException(new ModelException('Invalid Element')));

        // Execução
        $controller->dispatch(new Request());
    }
}
