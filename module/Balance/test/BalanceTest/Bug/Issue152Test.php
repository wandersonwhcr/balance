<?php

namespace BalanceTest\Bug;

use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use Balance\Model\EntryType;
use BalanceTest\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stdlib\Parameters;

class Issue152Test extends TestCase
{
    public function testEntryValueFormat()
    {
        // Gerenciador de Serviços
        $serviceLocator = Application::getApplication()->getServiceManager();

        // Camada de Modelo
        $mAccounts = $serviceLocator->get('Balance\Model\Persistence\Accounts');

        // Conta A
        $accountA = new Parameters([
            'type'        => AccountType::ACTIVE,
            'name'        => 'A',
            'description' => 'Issue 152 Account',
            'accumulate'  => BooleanType::NO,
        ]);
        // Salvar
        $mAccounts->save($accountA);

        // Conta B
        $accountB = new Parameters([
            'type'        => AccountType::ACTIVE,
            'name'        => 'B',
            'description' => 'Issue 152 Account',
            'accumulate'  => BooleanType::NO,
        ]);
        // Salvar
        $mAccounts->save($accountB);

        // Camada de Modelo
        $mPostings = $serviceLocator->get('Balance\Model\Persistence\Postings');

        // Lançamento
        $posting = new Parameters([
            'datetime'    => '10/10/2010 10:10:10',
            'description' => 'Issue 152 Posting',
            'entries'     => [
                [
                    'type'       => EntryType::CREDIT,
                    'account_id' => $accountA['id'],
                    'value'      => '100,00',
                ],
                [
                    'type'       => EntryType::DEBIT,
                    'account_id' => $accountB['id'],
                    'value'      => '100,00',
                ],
            ],
        ]);
        // Salvar
        $mPostings->save($posting);

        // Carregar Informações
        $result = $mPostings->find(new Parameters(['id' => $posting['id']]));

        // Capturar Primeira Entrada
        $element = current($result['entries']);
        // Verificações
        $this->assertEquals('100,00', $element['value']);

        // Capturar Segunda Entrada
        $element = next($result['entries']);
        // Verificações
        $this->assertEquals('100,00', $element['value']);
    }
}
