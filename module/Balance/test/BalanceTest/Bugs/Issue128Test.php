<?php

namespace BalanceTest\Bugs;

use Balance\Model\AccountType;
use Balance\Model\EntryType;
use Balance\Model\Persistence\Db\Postings;
use BalanceTest\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class Issue128Test extends TestCase
{
    protected function getPersistence()
    {
        // Inicialização
        $persistence = new Postings();

        // Gerenciador de Serviços
        $serviceManager = Application::getApplication()->getServiceManager();

        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        // Configurações
        $persistence->setServiceLocator($serviceLocator);

        // Tabelas
        $tbPostings = $serviceManager->get('Balance\Db\TableGateway\Postings');
        $tbAccounts = $serviceManager->get('Balance\Db\TableGateway\Accounts');
        $tbEntries  = $serviceManager->get('Balance\Db\TableGateway\Entries');

        // Configurações
        $serviceLocator
            ->setService('db', $serviceManager->get('db'))
            ->setService('Balance\Db\TableGateway\Postings', $tbPostings)
            ->setService('Balance\Db\TableGateway\Accounts', $tbAccounts)
            ->setService('Balance\Db\TableGateway\Entries', $tbEntries);

        // Limpeza
        $tbPostings->delete(function () {
            // Remover Todos
        });
        $tbAccounts->delete(function () {
            // Remover Todos
        });

        // Criar um Lançamento
        $tbPostings->insert([
            'datetime'    => '2010-10-10 10:10:10',
            'description' => 'Posting Description',
        ]);
        // Chave Primária
        $this->primary = (int) $tbPostings->getLastInsertValue();

        // Inserir Conta A
        $tbAccounts->insert([
            'name'        => 'Account A',
            'type'        => AccountType::ACTIVE,
            'description' => 'Account A Description',
            'position'    => 0,
            'accumulate'  => 0,
        ]);
        // Chave Primária
        $accountA = (int) $tbAccounts->getLastInsertValue();

        // Inserir Conta B
        $tbAccounts->insert([
            'name'        => 'Account B',
            'type'        => AccountType::ACTIVE,
            'description' => 'Account B Description',
            'position'    => 1,
            'accumulate'  => 0,
        ]);
        // Chave Primária
        $accountB = (int) $tbAccounts->getLastInsertValue();

        // Entrada 0
        $tbEntries->insert([
            'posting_id' => $this->primary,
            'account_id' => $accountA,
            'type'       => EntryType::CREDIT,
            'value'      => 9999999.99,
            'position'   => 0,
        ]);

        // Entrada 1
        $tbEntries->insert([
            'posting_id' => $this->primary,
            'account_id' => $accountB,
            'type'       => EntryType::DEBIT,
            'value'      => 9999999.99,
            'position'   => 1,
        ]);

        // Apresentação
        return $persistence;
    }

    public function testFindWithoutThousandSeparator()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta
        $result = $persistence->find(new Parameters(['id' => $this->primary]));

        // Capturar Entrada
        $element = current($result['entries']);
        // Verificação
        $this->assertEquals('9999999,99', $element['value']);

        // Capturar Entrada
        $element = next($result['entries']);
        // Verificação
        $this->assertEquals('9999999,99', $element['value']);
    }
}
