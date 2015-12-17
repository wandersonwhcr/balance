<?php

namespace Balance\Mvc\Controller;

use Balance\Model\Model;
use Balance\Mvc\Controller\ModelAwareInterface;
use Balance\Mvc\Controller\ModelAwareTrait;
use Balance\Mvc\Controller\RedirectRouteNameAwareInterface;
use Balance\Mvc\Controller\RedirectRouteNameAwareTrait;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;

class Controller extends AbstractActionController implements ModelAwareInterface, RedirectRouteNameAwareInterface
{
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
}

class AbstractControllerFactoryTest extends TestCase
{
    public function testCreateService()
    {
        // Inicializar Localizador de Serviço
        $serviceLocator    = new ServiceManager();
        $controllerLocator = (new ControllerManager())->setServiceLocator($serviceLocator);

        // Inicialização
        $form        = new Form();
        $formSearch  = new Form();
        $persistence = $this->getMock('Balance\Model\Persistence\PersistenceInterface');
        $model       = new Model($form, $formSearch, $persistence);

        // Camada de Modelo
        $serviceLocator->setService('Balance\Model\Model', $model);

        // Configurar Elemento
        $serviceLocator->setService('Config', [
            // Balance
            'balance_manager' => [
                'factories' => [
                    'Balance\Mvc\Controller\Controller' => [
                        'factory' => 'Balance\Mvc\Controller\AbstractControllerFactory',
                        'params'  => [
                            'model'               => 'Balance\Model\Model',
                            'redirect_route_name' => 'controller',
                        ],
                    ],
                ],
            ],
        ]);

        // Fábrica de Componentes
        $factory = new AbstractControllerFactory();
        $result  = $factory->canCreateServiceWithName(
            $controllerLocator,
            'controller',
            'Balance\Mvc\Controller\Controller'
        );
        // Verificações
        $this->assertTrue($result);

        // Construir Elemento
        $element = $factory->createServiceWithName(
            $controllerLocator,
            'controller',
            'Balance\Mvc\Controller\Controller'
        );
        // Verificações
        $this->assertInstanceOf('Balance\Mvc\Controller\Controller', $element);
        $this->assertSame($model, $element->getModel());
        $this->assertEquals('controller', $element->getRedirectRouteName());
    }
}
