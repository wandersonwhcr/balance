<?php

namespace Balance\Db\TableGateway;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;

class AbstractTableGatewayFactoryTest extends TestCase
{
    public function testCreateService()
    {
        // Inicializar Localizador de Serviço
        $serviceLocator = new ServiceManager();
        // Serviço Dependente: Banco de Dados
        $serviceLocator->setService('db', $this->getMock('Zend\Db\Adapter\AdapterInterface'));
        // Configurar Elemento
        $serviceLocator->setService('Config', array(
            'balance_manager' => array(
                'factories' => array(
                    'Balance\Db\TableGateway\Table' => array(
                        'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                        'params'  => array(
                            'table'       => 'table',
                            'primary_key' => 'id',
                            'sequence'    => 'table_id_seq',
                        ),
                    ),
                ),
            ),
        ));

        // Fábrica de Componentes
        $factory = new AbstractTableGatewayFactory();
        $result  = $factory->canCreateServiceWithName($serviceLocator, 'table', 'Balance\Db\TableGateway\Table');
        $this->assertTrue($result);
        // Construir Elemento
        $element = $factory->createServiceWithName($serviceLocator, 'table', 'Balance\Db\TableGateway\Table');
        $this->assertInstanceOf('Zend\Db\TableGateway\TableGateway', $element);
        $this->assertEquals('table', $element->getTable());
    }
}
