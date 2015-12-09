<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\AccountType;
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

        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        // Configurações
        $persistence->setServiceLocator($serviceLocator);

        // Banco de Dados
        $db = Application::getApplication()->getServiceManager()->get('db');
        // Configurações
        $serviceLocator->setService('db', $db);

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
        $insert->values(array(
            'name'        => 'ZZ Account Test',
            'type'        => AccountType::ACTIVE,
            'description' => 'Description',
            'position'    => 1,
            'accumulate'  => 0,
        ));
        // Execução
        $db->query($insert->getSqlString($db->getPlatform()))->execute();

        // Adicionar Conta AA
        $insert->values(array(
            'name'        => 'AA Account Test',
            'type'        => AccountType::ACTIVE,
            'description' => 'Description',
            'position'    => 0,
            'accumulate'  => 0,
        ));
        // Execução
        $db->query($insert->getSqlString($db->getPlatform()))->execute();

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
