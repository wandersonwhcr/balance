<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Mvc\Controller\IndexActionTrait;
use Balance\Mvc\Controller\ModelAwareInterface;
use Balance\Mvc\Controller\ModelAwareTrait;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class IndexActionController
    extends AbstractActionController
    implements ModelAwareInterface
{
    use IndexActionTrait;
    use ModelAwareTrait;
}

class IndexActionWithoutControllerController
    implements ModelAwareInterface, ServiceLocatorAwareInterface
{
    use IndexActionTrait;
    use ModelAwareTrait;
    use ServiceLocatorAwareTrait;
}

class IndexActionWithoutModelController
    extends AbstractActionController
{
    use IndexActionTrait;
    use ModelAwareTrait;
}

class IndexActionTraitTest extends TestCase
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
            ->method('getFormSearch')
            ->will($this->returnValue($form));

        // Pesquisa
        $model
            ->method('fetch')
            ->will($this->returnValue([['one' => 'two']]));

        // Controladora
        switch ($type) {
            case 'index-action-controller':
                $controller = new IndexActionController();
                break;
            case 'index-action-without-controller-controller':
                $controller = new IndexActionWithoutControllerController();
                break;
            case 'index-action-without-model-controller':
                $controller = new IndexActionWithoutModelController();
                break;
        }

        // Configurações
        $controller
            ->setModel($model)
            ->setServiceLocator($serviceLocator);

        // Apresentação
        return $controller;
    }

    public function testIndex()
    {
        // Inicialização
        $controller = $this->getController('index-action-controller');

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'index',
        ]));

        // Requisição
        $request = (new Request())
            ->setQuery(new Parameters(['one' => 'two']));

        // Execução
        $result = $controller->dispatch($request);

        // Verificações
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertEquals([['one' => 'two']], $result->elements);
        $this->assertSame($controller->getModel()->getFormSearch(), $result->form);
        $this->assertInstanceOf('Zend\Stdlib\Parameters', $result->params);
        $this->assertEquals('two', $result->params['one']);
    }

    public function testIndexWithoutController()
    {
        // Verificação
        $this->setExpectedException('Exception', 'Invalid Controller');

        // Inicialização
        $controller = $this->getController('index-action-without-controller-controller');

        // Execução
        $controller->indexAction();
    }

    public function testIndexWithoutModel()
    {
        // Verificação
        $this->setExpectedException('Exception', 'Invalid Controller');

        // Inicialização
        $controller = $this->getController('index-action-without-model-controller');

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch([
            'action' => 'index',
        ]));

        // Execução
        $controller->dispatch(new Request());
    }
}
