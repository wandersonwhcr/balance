<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\AccountType;
use Balance\Test\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceManager;

class AccountsTest extends TestCase
{
    public function testGetValueOptions()
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
