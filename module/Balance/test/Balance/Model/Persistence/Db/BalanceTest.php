<?php

namespace Balance\Model\Persistence\Db;

use Balance\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class BalanceTest extends TestCase
{
    public function testFetch()
    {
        // Inicialização
        $persistence = new Balance();

        // Localizador de Serviço
        $serviceLocator = new ServiceManager();
        // Configurações
        $persistence->setServiceLocator($serviceLocator);

        // Banco de Dados
        $serviceLocator->setService('db', Application::getApplication()->getServiceManager()->get('db'));

        // Consulta
        $result = $persistence->fetch(new Parameters());

        // Verificações
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('ACTIVE', $result);
        $this->assertArrayHasKey('PASSIVE', $result);
        $this->assertArrayHasKey('ACCUMULATE', $result);
        $this->assertInternalType('array', $result['ACTIVE']);
        $this->assertEmpty($result['ACTIVE']);
        $this->assertInternalType('array', $result['PASSIVE']);
        $this->assertEmpty($result['PASSIVE']);
        $this->assertInternalType('array', $result['ACCUMULATE']);
        $this->assertArrayHasKey('name', $result['ACCUMULATE']);
        $this->assertEquals('Lucro', $result['ACCUMULATE']['name']);
        $this->assertArrayHasKey('value', $result['ACCUMULATE']);
        $this->assertEquals(0, $result['ACCUMULATE']['value']);
        $this->assertArrayHasKey('currency', $result['ACCUMULATE']);
        $this->assertEquals('R$0,00', $result['ACCUMULATE']['currency']);
    }
}
