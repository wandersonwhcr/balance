<?php

namespace BalanceTest\Bugs;

use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use Balance\Model\Persistence\Db\Accounts;
use Balance\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Sql\Sql;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class Issue142Test extends TestCase
{
    protected function getPersistence()
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
        $tbPostings->delete(function () {
            // Remover Todos
        });
        $tbAccounts->delete(function () {
            // Remover Todos
        });

        // Conta A
        $accountA = new Parameters([
            'type'        => AccountType::ACTIVE,
            'name'        => 'A',
            'description' => '',
            'accumulate'  => BooleanType::NO,
        ]);
        // Salvar
        $persistence->save($accountA);

        // Conta B
        $accountB = new Parameters([
            'type'        => AccountType::ACTIVE,
            'name'        => 'B',
            'description' => '',
            'accumulate'  => BooleanType::NO,
        ]);
        // Salvar
        $persistence->save($accountB);

        // Conta C
        $accountC = new Parameters([
            'type'        => AccountType::ACTIVE,
            'name'        => 'C',
            'description' => '',
            'accumulate'  => BooleanType::NO,
        ]);
        // Salvar
        $persistence->save($accountC);

        // Salvamento
        $this->data = [
            'A' => $accountA,
            'B' => $accountB,
            'C' => $accountC,
        ];

        // Apresentação
        return $persistence;
    }

    public function testAccountOrder()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Ordenar o C para B (Então Coloca C com A antes dele)
        $persistence->order(new Parameters([
            'id'       => $this->data['C']['id'],
            'previous' => $this->data['A']['id'],
        ]));

        // Consulta
        $result = $persistence->fetch(new Parameters());

        // Primeiro Elemento
        $element = current($result);
        // Precisa ser o A
        $this->assertEquals($this->data['A']['name'], $element['name']);

        // Segundo Elemento
        $element = next($result);
        // Precisa ser o C
        $this->assertEquals($this->data['C']['name'], $element['name']);

        // Terceiro Elemento
        $element = next($result);
        // Precisa ser o B
        $this->assertEquals($this->data['B']['name'], $element['name']);
    }

    public function testAccountOrderLastToFirstPosition()
    {
        // Inicialização
        $persistence = $this->getPersistence();

        // Ordenar o C para a Primeira Posição (Ninguém Antes)
        $persistence->order(new Parameters([
            'id' => $this->data['C']['id'],
        ]));

        // Consulta
        $result = $persistence->fetch(new Parameters());

        // Primeiro Elemento
        $element = current($result);
        // Precisa ser o A
        $this->assertEquals($this->data['C']['name'], $element['name']);

        // Segundo Elemento
        $element = next($result);
        // Precisa ser o C
        $this->assertEquals($this->data['A']['name'], $element['name']);

        // Terceiro Elemento
        $element = next($result);
        // Precisa ser o B
        $this->assertEquals($this->data['B']['name'], $element['name']);

        // Banco de Dados
        $db = $persistence->getServiceLocator()->get('db');

        // Seletor
        $select = (new Sql($db))->select()
            ->from('accounts')
            ->columns(['position'])
            ->order(['position']);
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Processamento
        $result = [];
        foreach ($rowset as $row) {
            $result[] = (int) $row['position'];
        }

        // As Posições devem estar com NÙMEROS CORRETOS!
        $this->assertEquals([0, 1, 2], $result);
    }
}
