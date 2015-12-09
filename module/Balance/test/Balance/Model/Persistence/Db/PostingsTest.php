<?php

namespace Balance\Model\Persistence\Db;

use Balance\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class PostingsTest extends TestCase
{
    public function testFetch()
    {
        // Inicialização
        $persistence = new Postings();

        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        // Configuração
        $persistence->setServiceLocator($serviceLocator);

        // Banco de Dados
        $db = Application::getApplication()->getServiceManager()->get('db');
        // Configuração
        $serviceLocator->setService('db', $db);

        // Tabelas
        $tbPostings = Application::getApplication()->getServiceManager()->get('Balance\Db\TableGateway\Postings');

        // Limpeza
        $tbPostings->delete(function ($delete) {});

        // Inserir Lançamento 1
        $tbPostings->insert(array(
            'datetime'    => '2010-10-10 09:10:10',
            'description' => 'Posting 1',
        ));
        // Inserir Lançamento 2
        $tbPostings->insert(array(
            'datetime'    => '2010-10-10 10:10:10',
            'description' => 'Posting 2',
        ));

        // Consulta
        $result = $persistence->fetch(new Parameters())->getCurrentItems();

        // Verificações
        $this->assertCount(2, $result);

        // Elemento
        $element = current($result);
        // Verificações
        $this->assertEquals('2010-10-10 10:10:10', $element['datetime']);
        $this->assertEquals('Posting 2', $element['description']);

        // Elemento
        $element = next($result);
        // Verificações
        $this->assertEquals('2010-10-10 09:10:10', $element['datetime']);
        $this->assertEquals('Posting 1', $element['description']);
    }
}
