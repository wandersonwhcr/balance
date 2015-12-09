<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use Balance\Test\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class AccountsTest extends TestCase
{
    protected function getPersistence()
    {
        // Inicialização
        $persistence = new Accounts();

        // Conta ZZ
        $elementZZ = array(
            'name'        => 'ZZ Account Test',
            'type'        => AccountType::ACTIVE,
            'description' => 'Description',
            'position'    => 1,
            'accumulate'  => 0,
        );
        // Conta AA
        $elementAA = array(
            'name'        => 'AA Account Test',
            'type'        => AccountType::ACTIVE,
            'description' => 'Description',
            'position'    => 0,
            'accumulate'  => 0,
        );

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

        // Remover Todas as Contas
        $delete = (new Sql($db))->delete()
            ->from('accounts');
        // Execução
        $db->query($delete->getSqlString($db->getPlatform()))->execute();

        // Preparar Inserção
        $insert = (new Sql($db))->insert()
            ->into('accounts')
            ->columns(array('name', 'type', 'description', 'position', 'accumulate'));

        // Adicionar Conta ZZ
        $insert->values($elementZZ);
        // Execução
        $db->query($insert->getSqlString($db->getPlatform()))->execute();

        // Adicionar Conta AA
        $insert->values($elementAA);
        // Execução
        $db->query($insert->getSqlString($db->getPlatform()))->execute();

        // Consultar as Duas Chaves Primárias
        $select = (new Sql($db))->select()
            ->from('accounts')
            ->columns(array('id', 'name'));
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Consulta
        foreach ($rowset as $row) {
            switch ($row['name']) {
                case $elementAA['name']:
                    $elementAA['id'] = (int) $row['id'];
                    break;
                case $elementZZ['name']:
                    $elementZZ['id'] = (int) $row['id'];
                    break;
            }
        }
        // Configurar Elementos
        $this->data = array($elementAA, $elementZZ);

        // Apresentação
        return $persistence;
    }

    public function testFetch()
    {
        // Inicialização
        $persistence           = $this->getPersistence();
        $accountTypeDefinition = (new AccountType())->getDefinition();

        // Consulta
        $result = $persistence->fetch(new Parameters());

        // Verificações
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        // Capturar Primeira Posição
        $element = array_shift($result);
        // Verificações
        $this->assertInternalType('array', $element);
        $this->assertArrayHasKey('id', $element);
        $this->assertInternalType('int', $element['id']);
        $this->assertArrayHasKey('name', $element);
        $this->assertEquals('AA Account Test', $element['name']);
        $this->assertArrayHasKey('type', $element);
        $this->assertEquals($accountTypeDefinition[AccountType::ACTIVE], $element['type']);
        // Capturar Segunda Posição
        $element = array_shift($result);
        // Verificações
        $this->assertInternalType('array', $element);
        $this->assertArrayHasKey('id', $element);
        $this->assertInternalType('int', $element['id']);
        $this->assertArrayHasKey('name', $element);
        $this->assertEquals('ZZ Account Test', $element['name']);
        $this->assertArrayHasKey('type', $element);
        $this->assertEquals($accountTypeDefinition[AccountType::ACTIVE], $element['type']);
    }

    public function testFetchWithType()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta
        $result = $persistence->fetch(new Parameters(array('type' => AccountType::ACTIVE)));

        // Verificações
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        // Consulta
        $result = $persistence->fetch(new Parameters(array('type' => AccountType::PASSIVE)));

        // Verificações
        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    public function testFetchWithKeywords()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta
        $result = $persistence->fetch(new Parameters(array('keywords' => 'AA')));

        // Verificações
        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);

        // Consulta
        $result = $persistence->fetch(new Parameters(array('keywords' => 'Account Test')));

        // Verificações
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);

        // Consulta
        $result = $persistence->fetch(new Parameters(array('keywords' => 'FOOBAR')));

        // Verificações
        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);

        // Consulta
        $result = $persistence->fetch(new Parameters(array('keywords' => 'Description')));

        // Verificações
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
    }

    public function testFind()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Primeiro Elemento
        $element = array_shift($this->data);

        // Consulta
        $result = $persistence->find(new Parameters(array('id' => $element['id'])));

        // Verificações
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals($element['id'], $result['id']);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals($element['type'], $result['type']);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals($element['name'], $result['name']);
        $this->assertArrayHasKey('description', $result);
        $this->assertEquals($element['description'], $result['description']);
        $this->assertArrayHasKey('accumulate', $result);
        $this->assertEquals(BooleanType::NO, $result['accumulate']);

        // Segundo Elemento
        $element = array_shift($this->data);

        // Consulta
        $result = $persistence->find(new Parameters(array('id' => $element['id'])));

        // Verificações
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals($element['id'], $result['id']);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals($element['type'], $result['type']);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals($element['name'], $result['name']);
        $this->assertArrayHasKey('description', $result);
        $this->assertEquals($element['description'], $result['description']);
        $this->assertArrayHasKey('accumulate', $result);
        $this->assertEquals(BooleanType::NO, $result['accumulate']);
    }

    public function testFindWithoutPrimaryKey()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Unknown Primary Key');

        // Inicialização
        $persistence = $this->getPersistence();

        // Consulta
        $persistence->find(new Parameters());
    }

    public function testFindWithUnknownPrimaryKey()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Unknown Element');

        // Inicialização
        $persistence = $this->getPersistence();

        // Capturar Elementos
        $elementA = array_shift($this->data);
        $elementB = array_shift($this->data);
        // Gerar uma Chave Primária Desconhecida
        do {
            // Chave Randômica
            $id = rand();
        } while ($id == $elementA['id'] || $id == $elementB['id']);

        // Consulta
        $persistence->find(new Parameters(array('id' => $id)));
    }

    public function testRemove()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Capturar Elementos
        $elementA = array_shift($this->data);
        $elementB = array_shift($this->data);

        // Remoção
        $result = $persistence->remove(new Parameters(array('id' => $elementA['id'])));

        // Verificação
        $this->assertSame($persistence, $result);

        // Consulta
        $result = $persistence->fetch(new Parameters());

        // Verificação
        $this->assertCount(1, $result);

        // Remoção
        $persistence->remove(new Parameters(array('id' => $elementB['id'])));

        // Consulta
        $result = $persistence->fetch(new Parameters());

        // Verificação
        $this->assertCount(0, $result);
    }

    public function testRemoveWithoutPrimaryKey()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Unknown Primary Key');

        // Inicialização
        $persistence = $this->getPersistence();

        // Remoção
        $persistence->remove(new Parameters());
    }

    public function testRemoveUnknownElement()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Database Error');

        // Inicialização
        $persistence = $this->getPersistence();

        // Capturar Elementos
        $elementA = array_shift($this->data);
        $elementB = array_shift($this->data);
        // Gerar uma Chave Primária Desconhecida
        do {
            // Chave Randômica
            $id = rand();
        } while ($id == $elementA['id'] || $id == $elementB['id']);

        // Remoção
        $persistence->remove(new Parameters(array('id' => $id)));
    }

    public function testGetValueOptions()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Verificação de Tipagem
        $this->assertInstanceOf('Balance\Model\Persistence\ValueOptionsInterface', $persistence);

        // Consulta
        $result = $persistence->getValueOptions();

        // Consultar Resultados
        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        // Capturar Primeira Posição
        $element = array_shift($result);
        // Verificação
        $this->assertEquals('AA Account Test', $element);
        // Capturar Segunda Posição
        $element = array_shift($result);
        // Verificação
        $this->assertEquals('ZZ Account Test', $element);
    }
}
