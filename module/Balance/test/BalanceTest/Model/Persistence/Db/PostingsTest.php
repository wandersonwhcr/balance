<?php

namespace BalanceTest\Model\Persistence\Db;

use ArrayObject;
use Balance\Model\AccountType;
use Balance\Model\EntryType;
use Balance\Model\Persistence\Db\Postings;
use BalanceTest\Mvc\Application;
use IntlDateFormatter;
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

        // Formatador de Datas
        $formatter = new IntlDateFormatter(null, IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);

        // Configuração
        $serviceLocator
            ->setService('Balance\Db\TableGateway\Accounts', $tbAccounts)
            ->setService('Balance\Db\TableGateway\Postings', $tbPostings)
            ->setService('Balance\Db\TableGateway\Entries', $tbEntries);

        // Limpeza
        $tbPostings->delete(function () {
            // Remover Todos
        });
        $tbAccounts->delete(function () {
            // Remover Todos
        });

        // Chaves Primárias
        $primaries = [
            'postings' => [],
            'accounts' => [],
        ];

        // Inserir Conta 1
        $tbAccounts->insert([
            'name'        => 'Account AA',
            'type'        => AccountType::ACTIVE,
            'description' => 'Account AA Description',
            'position'    => 0,
            'accumulate'  => 0,
        ]);
        // Captura de Chave Primária
        $primaries['accounts']['aa'] = (int) $tbAccounts->getLastInsertValue();

        // Inserir Conta 2
        $tbAccounts->insert([
            'name'        => 'Account BB',
            'type'        => AccountType::ACTIVE,
            'description' => 'Account BB Description',
            'position'    => 1,
            'accumulate'  => 0,
        ]);
        // Captura de Chave Primária
        $primaries['accounts']['bb'] = (int) $tbAccounts->getLastInsertValue();

        // Inserir Lançamento 1
        $tbPostings->insert([
            'datetime'    => date('c', $formatter->parse('10/10/2010 09:10:10')),
            'description' => 'Posting XX',
        ]);
        // Captura de Chave Primária
        $primaries['postings']['xx'] = (int) $tbPostings->getLastInsertValue();

        // Inserir Lançamento 2
        $tbPostings->insert([
            'datetime'    => date('c', $formatter->parse('10/10/2010 10:10:10')),
            'description' => 'Posting YY',
        ]);
        // Captura de Chave Primária
        $primaries['postings']['yy'] = (int) $tbPostings->getLastInsertValue();

        // Relacionamento 0-0
        $tbEntries->insert([
            'posting_id' => $primaries['postings']['xx'],
            'account_id' => $primaries['accounts']['aa'],
            'type'       => EntryType::CREDIT,
            'value'      => 100,
            'position'   => 0,
        ]);
        // Relacionamento 0-1
        $tbEntries->insert([
            'posting_id' => $primaries['postings']['xx'],
            'account_id' => $primaries['accounts']['bb'],
            'type'       => EntryType::DEBIT,
            'value'      => 100,
            'position'   => 1,
        ]);

        // Relacionamento 1-0
        $tbEntries->insert([
            'posting_id' => $primaries['postings']['yy'],
            'account_id' => $primaries['accounts']['aa'],
            'type'       => EntryType::CREDIT,
            'value'      => 200,
            'position'   => 0,
        ]);
        // Relacionamento 1-1
        $tbEntries->insert([
            'posting_id' => $primaries['postings']['yy'],
            'account_id' => $primaries['accounts']['bb'],
            'type'       => EntryType::DEBIT,
            'value'      => 200,
            'position'   => 1,
        ]);

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
        $this->assertEquals('2010-10-10 10:10:10-03', $element['datetime']);
        $this->assertEquals('Posting YY', $element['description']);

        // Elemento
        $element = next($result);
        // Verificações
        $this->assertEquals('2010-10-10 09:10:10-03', $element['datetime']);
        $this->assertEquals('Posting XX', $element['description']);
    }

    public function testFetchWithKeywords()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta
        $result = $persistence->fetch(new Parameters(['keywords' => 'XX']))->getCurrentItems();

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
            ->fetch(new Parameters(['account_id' => $this->primaries['accounts']['aa']]))
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
            ->fetch(new Parameters(['datetime_end' => '10/10/2010 09:10:10']))
            ->getCurrentItems();

        // Verificações
        $this->assertCount(1, $result);

        // Elemento
        $element = current($result);
        // Verificações
        $this->assertEquals('Posting XX', $element['description']);

        // Consulta
        $result = $persistence
            ->fetch(new Parameters(['datetime_begin' => '10/10/2010 10:10:10']))
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
        $result = $persistence->fetch(new Parameters(['page' => 2]))->getCurrentItems();

        // Verificações
        $this->assertCount(2, $result);
    }

    public function testFind()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta de Elemento
        $element = $persistence->find(new Parameters(['id' => $this->primaries['postings']['xx']]));
        // Verificações
        $this->assertInstanceOf('ArrayAccess', $element);
        $this->assertEquals('Posting XX', $element['description']);

        // Consulta de Elemento
        $element = $persistence->find(new Parameters(['id' => $this->primaries['postings']['yy']]));
        // Verificações
        $this->assertInstanceOf('ArrayAccess', $element);
        $this->assertEquals('Posting YY', $element['description']);
    }

    public function testFindWithoutPrimaryKey()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Unknown Primary Key');

        // Inicialização
        $persistence = $this->getPersistence();

        // Consultar Elemento
        $persistence->find(new Parameters());
    }

    public function testFindUnknownElement()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Unknown Element');

        // Inicialização
        $persistence = $this->getPersistence();

        // Capturar Chave Primária Desconhecida
        do {
            // Gerar Nova Chave Randômica
            $id = rand();
        } while ($id === $this->primaries['postings']['xx'] || $id === $this->primaries['postings']['yy']);

        // Consultar Elemento
        $persistence->find(new Parameters(['id' => $id]));
    }

    public function testRemove()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Remover Lançamento
        $result = $persistence->remove(new Parameters(['id' => $this->primaries['postings']['xx']]));
        // Verificações
        $this->assertSame($persistence, $result);

        // Consulta
        $result = $persistence->fetch(new Parameters());
        // Verificações
        $this->assertCount(1, $result);

        // Remover Lançamento
        $persistence->remove(new Parameters(['id' => $this->primaries['postings']['yy']]));

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
        } while ($id === $this->primaries['postings']['xx'] || $id === $this->primaries['postings']['yy']);

        // Remover Lançamento
        $persistence->remove(new Parameters(['id' => $id]));
    }

    public function testSaveWithInsert()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Dados para Salvamento
        $data = new Parameters([
            'datetime'    => '10/10/2010 11:10:10',
            'description' => 'Posting ZZ',
            'entries'     => [
                [
                    'account_id' => $this->primaries['accounts']['aa'],
                    'type'       => EntryType::CREDIT,
                    'value'      => 'R$10,10',
                ],
                [
                    'account_id' => $this->primaries['accounts']['bb'],
                    'type'       => EntryType::DEBIT,
                    'value'      => 'R$10,10',
                ],
            ],
        ]);

        // Salvar Lançamento
        $result = $persistence->save($data);
        // Verificações
        $this->assertSame($persistence, $result);

        // Consulta
        $result = $persistence->fetch(new Parameters())->getCurrentItems();
        // Verificações
        $this->assertCount(3, $result);

        // Capturar Elemento
        $element = current($result);
        // Verificações
        $this->assertEquals('Posting ZZ', $element['description']);
    }

    public function testSaveWithUpdate()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Dados para Salvamento
        $data = new Parameters([
            'id'          => $this->primaries['postings']['xx'],
            'datetime'    => '10/10/2010 11:10:10',
            'description' => 'Posting ZZ',
            'entries'     => [
                [
                    'account_id' => $this->primaries['accounts']['aa'],
                    'type'       => EntryType::CREDIT,
                    'value'      => 'R$10,10',
                ],
                [
                    'account_id' => $this->primaries['accounts']['bb'],
                    'type'       => EntryType::DEBIT,
                    'value'      => 'R$10,10',
                ],
            ],
        ]);

        // Salvar Lançamento
        $result = $persistence->save($data);
        // Verificações
        $this->assertSame($persistence, $result);

        // Consulta
        $result = $persistence->fetch(new Parameters())->getCurrentItems();
        // Verificações
        $this->assertCount(2, $result);
    }

    public function testSaveWithException()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Database Error');

        // Inicialização
        $persistence = $this->getPersistence();

        // Capturar Chave Primária Desconhecida
        do {
            // Gerar Nova Chave Randômica
            $id = rand();
        } while ($id === $this->primaries['postings']['xx'] || $id === $this->primaries['postings']['yy']);

        // Salvar Lançamento
        $persistence->save(new Parameters(['id' => $id]));
    }

    public function testSaveWithSynchronizedEntries()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Tabela de Contas
        $tbAccounts = $persistence->getServiceLocator()->get('Balance\Db\TableGateway\Accounts');
        // Adicionar uma Nova Conta
        $tbAccounts->insert([
            'name'        => 'Account XYZ',
            'type'        => AccountType::ACTIVE,
            'description' => 'Account XYZ Description',
            'position'    => 2,
            'accumulate'  => 0,
        ]);
        // Chave Primária
        $pkAccount = $tbAccounts->getLastInsertValue();

        // Carregar Dados Salvos
        $data = $persistence->find(new Parameters(['id' => $this->primaries['postings']['xx']]));

        // Posição Atual
        $position = key($data['entries']);
        // Trocar Conta
        $data['entries'][$position]['account_id'] = $pkAccount;

        // Salvar Dados
        $persistence->save(new Parameters($data->getArrayCopy()));

        // Carregá-los Novamente
        $result = $persistence->find(new Parameters(['id' => $this->primaries['postings']['xx']]));

        // Dados Idênticos!
        $this->assertEquals($data, $result);
    }

    public function testTriggerSave()
    {
        // Camada de Persistência
        $persistence = $this->getPersistence();

        // Gerenciador de Eventos
        $eventManager = $persistence->getEventManager();

        // Dados
        $data = new Parameters([
            'counter'     => 0,
            'datetime'    => '10/10/2010 11:10:10',
            'description' => 'Posting ZZ',
            'entries'     => [
                [
                    'account_id' => $this->primaries['accounts']['aa'],
                    'type'       => EntryType::CREDIT,
                    'value'      => 'R$10,10',
                ],
                [
                    'account_id' => $this->primaries['accounts']['bb'],
                    'type'       => EntryType::DEBIT,
                    'value'      => 'R$10,10',
                ],
            ],
        ]);

        // Evento: Antes de Salvar
        $eventManager->attach('Balance\Model\Persistence\Db\Postings::beforeSave', function ($event) {
            // Atualizar Contador
            $event->getTarget()['counter'] += 10;
        });

        // Evento: Depois de Salvar
        $eventManager->attach('Balance\Model\Persistence\Db\Postings::afterSave', function ($event) {
            // Atualizar Contador
            $event->getTarget()['counter'] *= 10;
        });

        // Salvar Elemento
        $persistence->save($data);

        // Verificações
        $this->assertEquals(100, $data['counter']);
    }

    public function testTriggerFilters()
    {
        // Inicialização
        $persistence = $this->getPersistence();
        $counter     = new ArrayObject();

        // Atualizar Contador
        $counter['total'] = 1;

        // Evento: Antes de Efetuar a Consulta
        $persistence->getEventManager()
            ->attach('Balance\Model\Persistence\Db\Postings::afterFilters', function () use ($counter) {
                // Atualizar Contator
                $counter['total'] *= 10;
            });

        // Efetuar Consultas
        $persistence->fetch(new Parameters());
        $persistence->fetch(new Parameters());

        // Verificações
        $this->assertEquals(100, $counter['total']);
    }
}
