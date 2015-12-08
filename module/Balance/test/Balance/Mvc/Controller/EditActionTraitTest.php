<?php

namespace Balance\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceManager;

class EditActionController
    extends AbstractActionController
    implements ModelAwareInterface, RedirectRouteNameAwareInterface
{
    use EditActionTrait;
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
}

class EditActionTraitTest extends TestCase
{
    public function testEditAction()
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
        $controller = new EditActionController();

        // Configurar Parâmetros de Despacho
        $controller->getEvent()->setRouteMatch(new RouteMatch(array(
            'action' => 'edit',
        )));

        // Configurações
        $controller
            ->setModel($model)
            ->setServiceLocator($serviceLocator);

        // Execução
        $result = $controller->dispatch(new Request());

        // Verificações
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertEquals('add', $result->type);
        $this->assertSame($form, $result->form);
    }
}
