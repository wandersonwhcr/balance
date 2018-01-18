<?php

namespace BalanceTest\Bug;

use ArrayIterator;
use Balance\Model\Balance;
use BalanceTest\Mvc\Application;
use DateTime;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class Issue214Test extends TestCase
{
    protected function buildServiceLocator()
    {
        // Gerenciador de Serviços
        $serviceManager = Application::getApplication()->getServiceManager();

        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        // Configuração: Gerenciador de Elementos de Formulário
        $serviceLocator->setService('FormElementManager', $serviceManager->get('FormElementManager'));
        // Configuração: Gerenciador de Filtro de Entrada de Dados
        $serviceLocator->setService('InputFilterManager', $serviceManager->get('InputFilterManager'));

        // Camada de Persistência para Balanço
        $persistence = $this->getMock('Balance\Model\Persistence\PersistenceInterface');
        // Consulta de Balanço
        $persistence->method('fetch')
            ->will($this->returnValue(new ArrayIterator()));

        // Configuração: Camada de Persistência para Balanço
        $serviceLocator->setService('Balance\Model\Persistence\Balance', $persistence);

        // Apresentação
        return $serviceLocator;
    }

    public function testBalanceFetchDateTimePattern()
    {
        // Inicialização
        $model  = new Balance();
        $params = new Parameters();

        // Localizador de Serviços
        $model->setServiceLocator($this->buildServiceLocator());

        // Configurar Data e Hora
        $model->setDateTime(new DateTime('2012-12-31T23:59:59'));

        // Consulta
        $model->fetch($params);

        // Verificação
        $this->assertEquals('31/12/12 23:59:59', $params['datetime']);
    }
}
