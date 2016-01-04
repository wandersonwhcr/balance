<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Model\ModelException;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class EditActionTraitTest extends TestCase
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

        // Formulário
        $form = new Form();
        // Capturar Formulário
        $model
            ->method('getForm')
            ->will($this->returnValue($form));

        // Controladora
        switch ($type) {
            case 'edit-action-controller':
                $controller = new EditActionController();
                break;
            case 'edit-action-without-controller-controller':
                $controller = new EditActionWithoutControllerController();
                break;
            case 'edit-action-without-model-controller':
                $controller = new EditActionWithoutModelController();
                break;
            case 'edit-action-without-redirect-route-name-controller':
                $controller = new EditActionWithoutRedirectRouteNameController();
                break;
        }

        // Configurações
        $controller
            ->setModel($model)
            ->setServiceLocator($serviceLocator);

        // Apresentação
        return $controller;
    }

    public function testEditAction()
    {
        // Inicialização
        $controller = $this->getController('edit-action-controller');

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'edit',
        ]));

        // Execução
        $result = $controller->dispatch(new Request());

        // Verificações
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertEquals('add', $result->type);
        $this->assertSame($controller->getModel()->getForm(), $result->form);
    }

    public function testEditActionWithParameters()
    {
        // Inicialização
        $controller = $this->getController('edit-action-controller');

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'edit',
            'one'    => 'two',
        ]));

        // Execução
        $result = $controller->dispatch(new Request());

        // Verificações
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertEquals('edit', $result->type);
        $this->assertSame($controller->getModel()->getForm(), $result->form);
    }

    public function testEditActionWithParametersAndException()
    {
        // Inicialização
        $controller = $this->getController('edit-action-controller');

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'edit',
            'one'    => 'two',
        ]));

        // Plugin de Redirecionamento
        $redirect = $this->getMock('Zend\Mvc\Controller\Plugin\Redirect');
        // Configurações
        $controller->getPluginManager()->setService('redirect', $redirect);

        // Camada de Modelo
        $controller->getModel()
            ->method('load')
            ->will($this->throwException(new ModelException('Invalid Element')));

        // Execução
        $result = $controller->dispatch(new Request());

        // Verificação
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
    }

    public function testEditActionWithPost()
    {
        // Inicialização
        $controller = $this->getController('edit-action-controller');

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'edit',
        ]));

        // Plugin de Redirecionamento
        $redirect = $this->getMock('Zend\Mvc\Controller\Plugin\Redirect');
        // Configurações
        $controller->getPluginManager()->setService('redirect', $redirect);

        // Requisição
        $request = (new Request())
            ->setMethod('POST')
            ->setPost(new Parameters(['one' => 'two']));

        // Execução
        $result = $controller->dispatch($request);

        // Verificação
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
    }

    public function testEditActionWithPostAndException()
    {
        // Inicialização
        $controller = $this->getController('edit-action-controller');

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'edit',
        ]));

        // Plugin de Redirecionamento
        $redirect = $this->getMock('Zend\Mvc\Controller\Plugin\Redirect');
        // Configurações
        $controller->getPluginManager()->setService('redirect', $redirect);

        // Camada de Modelo
        $controller->getModel()
            ->method('save')
            ->will($this->throwException(new ModelException('Invalid Element')));

        // Requisição
        $request = (new Request())
            ->setMethod('POST')
            ->setPost(new Parameters(['one' => 'two']));

        // Execução
        $result = $controller->dispatch($request);

        // Verificação
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
    }

    public function testEditActionWithoutController()
    {
        // Verificações
        $this->setExpectedException('Exception', 'Invalid Controller');

        // Inicialização
        $controller = $this->getController('edit-action-without-controller-controller');

        // Execução
        $controller->editAction();
    }

    public function testEditActionWithoutModel()
    {
        // Verificações
        $this->setExpectedException('Exception', 'Invalid Controller');

        // Inicialização
        $controller = $this->getController('edit-action-without-model-controller');

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'edit',
        ]));

        // Execução
        $controller->dispatch(new Request());
    }

    public function testEditActionWithoutRedirectRouteNameModel()
    {
        // Verificações
        $this->setExpectedException('Exception', 'Invalid Controller');

        // Inicialização
        $controller = $this->getController('edit-action-without-redirect-route-name-controller');

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'edit',
        ]));

        // Execução
        $controller->dispatch(new Request());
    }

    public function testAfterViewModel()
    {
        $controller = $this->getController('edit-action-controller');

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'edit',
        ]));

        // Criar um Capturador
        $handler = new Parameters();

        // Evento: Após o ViewModel
        $controller->getEventManager()
            ->attach('Balance\Mvc\Controller\EditAction::afterViewModel', function ($event) use ($handler) {
                $handler['target'] = $event->getTarget();
            });

        // Execução
        $result = $controller->dispatch(new Request());

        // Verificações
        $this->assertArrayHasKey('target', $handler);
        $this->assertSame($result, $handler['target']);
    }
}
