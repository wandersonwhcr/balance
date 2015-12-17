<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\AccountType;
use Balance\Model\EntryType;
use Balance\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class BalanceTest extends TestCase
{
    public function getPersistence()
    {
        // Inicialização
        $persistence = new Balance();

        // Localizador de Serviço
        $serviceLocator = new ServiceManager();
        // Configurações
        $persistence->setServiceLocator($serviceLocator);

        // Banco de Dados
        $db = Application::getApplication()->getServiceManager()->get('db');
        // Configurações
        $serviceLocator->setService('db', $db);

        // Limpeza de Lançamentos
        $delete = (new Sql($db))->delete()
            ->from('postings');
        // Execução
        $db->query($delete->getSqlString($db->getPlatform()))->execute();

        // Limpeza de Contas
        $delete = (new Sql($db))->delete()
            ->from('accounts');
        // Execução
        $db->query($delete->getSqlString($db->getPlatform()))->execute();

        // Tabelas
        $tbAccounts = Application::getApplication()->getServiceManager()->get('Balance\Db\TableGateway\Accounts');
        $tbPostings = Application::getApplication()->getServiceManager()->get('Balance\Db\TableGateway\Postings');
        $tbEntries  = Application::getApplication()->getServiceManager()->get('Balance\Db\TableGateway\Entries');

        // Chaves Primárias
        $primaries = [
            'accounts' => [],
            'postings' => [],
        ];

        // Receitas
        $tbAccounts->insert([
            'name'        => 'Receitas',
            'type'        => AccountType::PASSIVE,
            'description' => 'Descrição de Receitas',
            'position'    => 0,
            'accumulate'  => 1,
        ]);
        // Captura de Chave Primária
        $primaries['accounts']['receitas'] = (int) $tbAccounts->getLastInsertValue();

        // Despesas
        $tbAccounts->insert([
            'name'        => 'Despesas',
            'type'        => AccountType::PASSIVE,
            'description' => 'Descrição de Despesas',
            'position'    => 1,
            'accumulate'  => 1,
        ]);
        // Captura de Chave Primária
        $primaries['accounts']['despesas'] = (int) $tbAccounts->getLastInsertValue();

        // Banco
        $tbAccounts->insert([
            'name'        => 'Banco',
            'type'        => AccountType::ACTIVE,
            'description' => 'Descrição de Banco',
            'position'    => 2,
            'accumulate'  => 0,
        ]);
        // Captura de Chave Primária
        $primaries['accounts']['banco'] = (int) $tbAccounts->getLastInsertValue();

        // Caixa
        $tbAccounts->insert([
            'name'        => 'Caixa',
            'type'        => AccountType::ACTIVE,
            'description' => 'Descrição de Caixa',
            'position'    => 3,
            'accumulate'  => 0,
        ]);
        // Captura de Chave Primária
        $primaries['accounts']['caixa'] = (int) $tbAccounts->getLastInsertValue();

        // Inicializar Banco
        $tbPostings->insert([
            'datetime'    => '2010-10-10 10:00:00',
            'description' => 'Inicialização de Banco',
        ]);
        // Captura de Chave Primária
        $primaries['postings']['banco_init'] = (int) $tbPostings->getLastInsertValue();

        // Sacar Dinheiro do Banco
        $tbPostings->insert([
            'datetime'    => '2010-10-10 10:05:00',
            'description' => 'Saque de Valores do Banco',
        ]);
        // Captura de Chave Primária
        $primaries['postings']['banco_remove'] = (int) $tbPostings->getLastInsertValue();

        // Pagar uma Conta
        $tbPostings->insert([
            'datetime'    => '2010-10-10 10:05:00',
            'description' => 'Pagamento de Conta',
        ]);
        // Captura de Chave Primária
        $primaries['postings']['caixa_remove'] = (int) $tbPostings->getLastInsertValue();

        // Entrada: Inicializar Banco
        $tbEntries->insert([
            'posting_id' => $primaries['postings']['banco_init'],
            'account_id' => $primaries['accounts']['banco'],
            'type'       => EntryType::DEBIT,
            'value'      => 1000,
            'position'   => 0,
        ]);
        // Entrada: Inicializar Banco
        $tbEntries->insert([
            'posting_id' => $primaries['postings']['banco_init'],
            'account_id' => $primaries['accounts']['receitas'],
            'type'       => EntryType::CREDIT,
            'value'      => 1000,
            'position'   => 1,
        ]);

        // Entrada: Sacar Dinheiro do Banco
        $tbEntries->insert([
            'posting_id' => $primaries['postings']['banco_remove'],
            'account_id' => $primaries['accounts']['banco'],
            'type'       => EntryType::CREDIT,
            'value'      => 500,
            'position'   => 0,
        ]);
        // Entrada: Sacar Dinheiro do Banco
        $tbEntries->insert([
            'posting_id' => $primaries['postings']['banco_remove'],
            'account_id' => $primaries['accounts']['caixa'],
            'type'       => EntryType::DEBIT,
            'value'      => 500,
            'position'   => 1,
        ]);

        // Entrada: Pagar uma Conta
        $tbEntries->insert([
            'posting_id' => $primaries['postings']['caixa_remove'],
            'account_id' => $primaries['accounts']['caixa'],
            'type'       => EntryType::CREDIT,
            'value'      => 200,
            'position'   => 0,
        ]);
        // Entrada: Pagar uma Conta
        $tbEntries->insert([
            'posting_id' => $primaries['postings']['caixa_remove'],
            'account_id' => $primaries['accounts']['despesas'],
            'type'       => EntryType::DEBIT,
            'value'      => 200,
            'position'   => 1,
        ]);

        // Apresentação
        return $persistence;
    }

    public function testFetch()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta
        $result = $persistence->fetch(new Parameters(['datetime' => '10/10/2011 00:00:00']));

        // Verificações
        $this->assertInstanceOf('Traversable', $result);
        $this->assertArrayHasKey('ACTIVE', $result);
        $this->assertArrayHasKey('PASSIVE', $result);
        $this->assertArrayHasKey('ACCUMULATE', $result);

        $this->assertInstanceOf('ArrayIterator', $result['ACTIVE']);
        $this->assertNotEmpty($result['ACTIVE']);

        $element = current($result['ACTIVE']);
        $this->assertInternalType('array', $element);
        $this->assertArrayHasKey('name', $element);
        $this->assertEquals('Banco', $element['name']);
        $this->assertArrayHasKey('value', $element);
        $this->assertEquals(500, $element['value']);
        $this->assertArrayHasKey('currency', $element);
        $this->assertEquals('R$500,00', $element['currency']);

        $element = next($result['ACTIVE']);
        $this->assertInternalType('array', $element);
        $this->assertArrayHasKey('name', $element);
        $this->assertEquals('Caixa', $element['name']);
        $this->assertArrayHasKey('value', $element);
        $this->assertEquals(300, $element['value']);
        $this->assertArrayHasKey('currency', $element);
        $this->assertEquals('R$300,00', $element['currency']);

        $this->assertInstanceOf('Traversable', $result['PASSIVE']);
        $this->assertEmpty($result['PASSIVE']);

        $this->assertInstanceOf('ArrayObject', $result['ACCUMULATE']);
        $this->assertArrayHasKey('name', $result['ACCUMULATE']);
        $this->assertEquals('Lucro', $result['ACCUMULATE']['name']);
        $this->assertArrayHasKey('value', $result['ACCUMULATE']);
        $this->assertEquals(800, $result['ACCUMULATE']['value']);
        $this->assertArrayHasKey('currency', $result['ACCUMULATE']);
        $this->assertEquals('R$800,00', $result['ACCUMULATE']['currency']);
    }
}
