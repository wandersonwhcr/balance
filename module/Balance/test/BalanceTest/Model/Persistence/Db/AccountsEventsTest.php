<?php

namespace BalanceTest\Model\Persistence\Db;

use ArrayObject;
use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use Balance\Model\Persistence\Db\Accounts;
use BalanceTest\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class AccountsEventsTest extends TestCase
{
    protected function setUp()
    {
        // Inicialização
        $persistence = new Accounts();

        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        // Configurações
        $persistence->setServiceLocator($serviceLocator);

        // Banco de Dados
        $db = Application::getApplication()->getServiceManager()->get('db');
        // Configurações
        $serviceLocator->setService('db', $db);

        // Tabela de Contas
        $tbAccounts = Application::getApplication()->getServiceManager()->get('Balance\Db\TableGateway\Accounts');
        // Configurações
        $serviceLocator->setService('Balance\Db\TableGateway\Accounts', $tbAccounts);

        // Tabela de Lançamentos
        $tbPostings = Application::getApplication()->getServiceManager()->get('Balance\Db\TableGateway\Postings');
        // Não Precisamos Informar os Lançamentos

        // Remover Todos os Lançamentos
        $tbPostings->delete(function () {
            // Remover Todos Elementos
        });
        // Remover Todas as Contas
        $tbAccounts->delete(function () {
            // Remover Todos Elementos
        });

        // Configuração
        $this->persistence = $persistence;
    }

    protected function tearDown()
    {
        unset($this->persistence);
    }

    public function testTriggerSave()
    {
        // Gerenciador de Eventos
        $eventManager = $this->persistence->getEventManager();

        // Dados
        $data = new Parameters([
            'type'        => AccountType::PASSIVE,
            'name'        => 'FB Account Test',
            'description' => 'Description of the Account',
            'accumulate'  => BooleanType::YES,
            'counter'     => 0,
        ]);

        // Evento: Antes de Salvar
        $eventManager->attach('beforeSave', function ($event) {
            // Atualizar Contador
            $event->getTarget()['counter'] += 10;
        });

        // Evento: Depois de Salvar
        $eventManager->attach('afterSave', function ($event) {
            // Atualizar Contador
            $event->getTarget()['counter'] *= 10;
        });

        // Salvar Elemento
        $this->persistence->save($data);

        // Verificações
        $this->assertEquals(100, $data['counter']);
    }

    public function testTriggerFilters()
    {
        // Inicialização
        $counter = new ArrayObject();

        // Atualizar Contador
        $counter['total'] = 1;

        // Evento: Antes de Efetuar a Consulta
        $this->persistence->getEventManager()
            ->attach('afterFilters', function () use ($counter) {
                // Atualizar Contator
                $counter['total'] *= 10;
            });

        // Efetuar Consultas
        $this->persistence->fetch(new Parameters());
        $this->persistence->fetch(new Parameters());

        // Verificações
        $this->assertEquals(100, $counter['total']);
    }

    public function testTriggerFiltersWithParams()
    {
        // Inicialização
        $container = new Parameters();

        // Evento: Após Filtragem
        $this->persistence->getEventManager()
            ->attach('afterFilters', function ($event) use ($container) {
                // Inicialização
                $params = $event->getParams();
                // Configurado?
                if (! empty($params['foo'])) {
                    // Captura
                    $container['foo'] = $params['foo'];
                }
            });

        // Efetuar Consulta
        $this->persistence->fetch(new Parameters(['foo' => 'bar']));

        // Verificação
        $this->assertEquals('bar', $container['foo']);
    }
}
