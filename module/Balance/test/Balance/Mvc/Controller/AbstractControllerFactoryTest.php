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
        $controller        = $this
            ->getMockBuilder('Zend\Mvc\Controller\AbstractActionController')
            ->setMockClassName('Balance_Mvc_Controller_Controller')
            ->getMock();

        // Configurar Elemento
        $serviceLocator->setService('Config', array(
            // Balance
            'balance_manager' => array(
                'factories' => array(
                    'Balance_Mvc_Controller_Controller' => array(
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
            'Balance_Mvc_Controller_Controller'
        );
        // Verificações
        $this->assertTrue($result);

        // Construir Elemento
        $element = $factory->createServiceWithName(
            $controllerLocator,
            'controller',
            'Balance_Mvc_Controller_Controller'
        );
        // Verificações
        $this->assertInstanceOf('Zend\Mvc\Controller\AbstractActionController', $element);
    }
}
