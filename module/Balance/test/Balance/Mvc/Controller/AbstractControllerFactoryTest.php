<?php

namespace Balance\Mvc\Controller;

use Balance\Model\Model;
use Balance\Mvc\Controller\ModelAwareInterface;
use Balance\Mvc\Controller\ModelAwareTrait;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;

class Controller extends AbstractActionController implements ModelAwareInterface
{
    use ModelAwareTrait;
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
        $serviceLocator->setService('Config', array(
            // Balance
            'balance_manager' => array(
                'factories' => array(
                    'Balance\Mvc\Controller\Controller' => array(
                        'factory' => 'Balance\Mvc\Controller\AbstractControllerFactory',
                        'params'  => array(
                            'model'               => 'Balance\Model\Model',
                            'redirect_route_name' => 'controller',
                        ),
                    ),
                ),
            ),
        ));

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
    }
}
