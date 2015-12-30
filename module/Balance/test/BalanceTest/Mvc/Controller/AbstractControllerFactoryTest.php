<?php

namespace BalanceTest\Mvc\Controller;

use Balance\Model\Model;
use Balance\Mvc\Controller\AbstractControllerFactory;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceManager;

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
        $model       = new Model($persistence);

        // Configurações
        $model->setForm($form)->setFormSearch($formSearch);

        // Camada de Modelo
        $serviceLocator->setService('Balance\Model\Model', $model);

        // Configurar Elemento
        $serviceLocator->setService('Config', [
            // Balance
            'balance_manager' => [
                'factories' => [
                    'BalanceTest\Mvc\Controller\Controller' => [
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
            'BalanceTest\Mvc\Controller\Controller'
        );
        // Verificações
        $this->assertTrue($result);

        // Construir Elemento
        $element = $factory->createServiceWithName(
            $controllerLocator,
            'controller',
            'BalanceTest\Mvc\Controller\Controller'
        );
        // Verificações
        $this->assertInstanceOf('BalanceTest\Mvc\Controller\Controller', $element);
        $this->assertSame($model, $element->getModel());
        $this->assertEquals('controller', $element->getRedirectRouteName());
    }
}
