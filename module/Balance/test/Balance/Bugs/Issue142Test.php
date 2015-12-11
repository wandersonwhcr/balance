<?php

namespace Balance\Bugs;

use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use Balance\Model\Persistence\Db\Accounts;
use Balance\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class Issue142Test extends TestCase
{
    public function testAccountOrder()
    {
        // Inicialização
        $persistence = new Accounts();

        // Gerenciador de Serviços
        $serviceManager = Application::getApplication()->getServiceManager();

        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        // Configurações
        $persistence->setServiceLocator($serviceLocator);

        // Banco de Dados
        $db         = $serviceManager->get('db');
        $tbAccounts = $serviceManager->get('Balance\Db\TableGateway\Accounts');
        $tbPostings = $serviceManager->get('Balance\Db\TableGateway\Postings');
        // Configurações
        $serviceLocator
            ->setService('db', $db)
            ->setService('Balance\Db\TableGateway\Accounts', $tbAccounts);

        // Limpeza
        $tbPostings->delete(function () {});
        $tbAccounts->delete(function () {});

        // Conta A
        $accountA = new Parameters(array(
            'type'        => AccountType::ACTIVE,
            'name'        => 'A',
            'description' => '',
            'accumulate'  => BooleanType::NO,
        ));
        // Salvar
        $persistence->save($accountA);

        // Conta B
        $accountB = new Parameters(array(
            'type'        => AccountType::ACTIVE,
            'name'        => 'B',
            'description' => '',
            'accumulate'  => BooleanType::NO,
        ));
        // Salvar
        $persistence->save($accountB);

        // Conta C
        $accountC = new Parameters(array(
            'type'        => AccountType::ACTIVE,
            'name'        => 'C',
            'description' => '',
            'accumulate'  => BooleanType::NO,
        ));
        // Salvar
        $persistence->save($accountC);

        // Ordenar o C para B (Então Coloca C com A antes dele)
        $persistence->order(new Parameters(array(
            'id'       => $accountC['id'],
            'previous' => $accountA['id'],
        )));

        // Consulta
        $result = $persistence->fetch(new Parameters());

        // Primeiro Elemento
        $element = current($result);
        // Precisa ser o A
        $this->assertEquals($accountA['name'], $element['name']);

        // Segundo Elemento
        $element = next($result);
        // Precisa ser o C
        $this->assertEquals($accountC['name'], $element['name']);

        // Terceiro Elemento
        $element = next($result);
        // Precisa ser o B
        $this->assertEquals($accountB['name'], $element['name']);
    }
}
