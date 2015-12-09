<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\AccountType;
use Balance\Model\EntryType;
use Balance\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class PostingsTest extends TestCase
{
    protected function getPersistence()
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
        $tbAccounts = Application::getApplication()->getServiceManager()->get('Balance\Db\TableGateway\Accounts');
        $tbPostings = Application::getApplication()->getServiceManager()->get('Balance\Db\TableGateway\Postings');
        $tbEntries  = Application::getApplication()->getServiceManager()->get('Balance\Db\TableGateway\Entries');

        // Configuração
        $serviceLocator
            ->setService('Balance\Db\TableGateway\Accounts', $tbAccounts)
            ->setService('Balance\Db\TableGateway\Postings', $tbPostings)
            ->setService('Balance\Db\TableGateway\Entries', $tbEntries);

        // Limpeza
        $tbPostings->delete(function ($delete) {});
        $tbAccounts->delete(function ($delete) {});

        // Chaves Primárias
        $primaries = array(
            'postings' => array(),
            'accounts' => array(),
        );

        // Inserir Conta 1
        $tbAccounts->insert(array(
            'name'        => 'Account AA',
            'type'        => AccountType::ACTIVE,
            'description' => 'Account AA Description',
            'position'    => 0,
            'accumulate'  => 0,
        ));
        // Captura de Chave Primária
        $primaries['accounts']['aa'] = (int) $tbAccounts->getLastInsertValue();

        // Inserir Conta 2
        $tbAccounts->insert(array(
            'name'        => 'Account BB',
            'type'        => AccountType::ACTIVE,
            'description' => 'Account BB Description',
            'position'    => 1,
            'accumulate'  => 0,
        ));
        // Captura de Chave Primária
        $primaries['accounts']['bb'] = (int) $tbAccounts->getLastInsertValue();

        // Inserir Lançamento 1
        $tbPostings->insert(array(
            'datetime'    => '2010-10-10 09:10:10',
            'description' => 'Posting XX',
        ));
        // Captura de Chave Primária
        $primaries['postings']['xx'] = (int) $tbPostings->getLastInsertValue();

        // Inserir Lançamento 2
        $tbPostings->insert(array(
            'datetime'    => '2010-10-10 10:10:10',
            'description' => 'Posting YY',
        ));
        // Captura de Chave Primária
        $primaries['postings']['yy'] = (int) $tbPostings->getLastInsertValue();

        // Relacionamento 0-0
        $tbEntries->insert(array(
            'posting_id' => $primaries['postings']['xx'],
            'account_id' => $primaries['accounts']['aa'],
            'type'       => EntryType::CREDIT,
            'value'      => 100,
            'position'   => 0,
        ));
        // Relacionamento 0-1
        $tbEntries->insert(array(
            'posting_id' => $primaries['postings']['xx'],
            'account_id' => $primaries['accounts']['bb'],
            'type'       => EntryType::DEBIT,
            'value'      => 100,
            'position'   => 1,
        ));

        // Relacionamento 1-0
        $tbEntries->insert(array(
            'posting_id' => $primaries['postings']['yy'],
            'account_id' => $primaries['accounts']['aa'],
            'type'       => EntryType::CREDIT,
            'value'      => 200,
            'position'   => 0,
        ));
        // Relacionamento 1-1
        $tbEntries->insert(array(
            'posting_id' => $primaries['postings']['yy'],
            'account_id' => $primaries['accounts']['bb'],
            'type'       => EntryType::DEBIT,
            'value'      => 200,
            'position'   => 1,
        ));

        // Configuração
        $this->primaries = $primaries;

        // Apresentação
        return $persistence;
    }

    public function testFetch()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta
        $result = $persistence->fetch(new Parameters())->getCurrentItems();

        // Verificações
        $this->assertCount(2, $result);

        // Elemento
        $element = current($result);
        // Verificações
        $this->assertEquals('2010-10-10 10:10:10', $element['datetime']);
        $this->assertEquals('Posting YY', $element['description']);

        // Elemento
        $element = next($result);
        // Verificações
        $this->assertEquals('2010-10-10 09:10:10', $element['datetime']);
        $this->assertEquals('Posting XX', $element['description']);
    }

    public function testFetchWithKeywords()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta
        $result = $persistence->fetch(new Parameters(array('keywords' => 'XX')))->getCurrentItems();

        // Verificações
        $this->assertCount(1, $result);

        // Elemento
        $element = current($result);
        // Verificações
        $this->assertEquals('Posting XX', $element['description']);
    }

    public function testFetchWithAccount()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta
        $result = $persistence
            ->fetch(new Parameters(array('account_id' => $this->primaries['accounts']['aa'])))
            ->getCurrentItems();

        // Verificações
        $this->assertCount(2, $result);

        // Elemento
        $element = current($result);
        // Verificações
        $this->assertEquals('Posting YY', $element['description']);
    }

    public function testFetchWithDatetime()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta
        $result = $persistence
            ->fetch(new Parameters(array('datetime_end' => '10/10/2010 09:10:10')))
            ->getCurrentItems();

        // Verificações
        $this->assertCount(1, $result);

        // Elemento
        $element = current($result);
        // Verificações
        $this->assertEquals('Posting XX', $element['description']);

        // Consulta
        $result = $persistence
            ->fetch(new Parameters(array('datetime_begin' => '10/10/2010 10:10:10')))
            ->getCurrentItems();

        // Verificações
        $this->assertCount(1, $result);

        // Elemento
        $element = current($result);
        // Verificações
        $this->assertEquals('Posting YY', $element['description']);
    }

    public function testFetchWithPage()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta de Página Inválida
        $result = $persistence->fetch(new Parameters(array('page' => 2)))->getCurrentItems();

        // Verificações
        $this->assertCount(2, $result);
    }

    public function testRemove()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Remover Lançamento
        $result = $persistence->remove(new Parameters(array('id' => $this->primaries['postings']['xx'])));
        // Verificações
        $this->assertSame($persistence, $result);

        // Consulta
        $result = $persistence->fetch(new Parameters());
        // Verificações
        $this->assertCount(1, $result);

        // Remover Lançamento
        $persistence->remove(new Parameters(array('id' => $this->primaries['postings']['yy'])));

        // Consulta
        $result = $persistence->fetch(new Parameters());
        // Verificações
        $this->assertCount(0, $result);
    }

    public function testRemoveWithoutPrimaryKey()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Unknown Primary Key');

        // Inicialização
        $persistence = $this->getPersistence();

        // Remover Lançamento
        $persistence->remove(new Parameters());
    }

    public function testRemoveUnknownElement()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Unknown Element');

        // Inicialização
        $persistence = $this->getPersistence();

        // Capturar Chave Primária Desconhecida
        do {
            // Gerar Nova Chave Randômica
            $id = rand();
        } while ($id == $this->primaries['postings']['xx'] || $id == $this->primaries['postings']['yy']);

        // Remover Lançamento
        $persistence->remove(new Parameters(array('id' => $id)));
    }
}
