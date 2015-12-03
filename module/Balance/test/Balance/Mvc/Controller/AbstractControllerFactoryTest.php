<?php

namespace Balance\Mvc\Controller;

use Balance\Form\Form;
use Balance\Model\Model;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;

class AbstractControllerFactoryTest extends TestCase
{
    public function testCreateService()
    {
        // Inicializar Localizador de Serviço
        $serviceLocator    = new ServiceManager();
        $controllerLocator = (new ControllerManager())->setServiceLocator($serviceLocator);
        $controller        = $this->getMock('Zend\Mvc\Controller\AbstractActionController');
        $classname         = get_class($controller);

        // Configurar Elemento
        $serviceLocator->setService('Config', array(
            // Balance
            'balance_manager' => array(
                'factories' => array(
                    $classname => array(
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
            $classname
        );
        // Verificações
        $this->assertTrue($result);

        // Construir Elemento
        $element = $factory->createServiceWithName(
            $controllerLocator,
            'controller',
            $classname
        );
        // Verificações
        $this->assertInstanceOf('Zend\Mvc\Controller\AbstractActionController', $element);
    }
}
