<?php

namespace BalanceTest\Bug;

use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use Balance\Model\EntryType;
use Balance\Model\ModelException;
use BalanceTest\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Stdlib\Parameters;

class Issue151Test extends TestCase
{
    public function testSimpleSavePosting()
    {
        // Localizador de Serviços
        $serviceLocator = Application::getApplication()->getServiceManager();

        // Camada de Modelo
        $mAccounts = $serviceLocator->get('Balance\Model\Persistence\Accounts');

        // Conta A
        $accountA = new Parameters([
            'type'        => AccountType::ACTIVE,
            'name'        => 'Issue 151 A',
            'description' => 'Account A',
            'accumulate'  => BooleanType::NO,
        ]);
        // Salvar
        $mAccounts->save($accountA);

        // Conta B
        $accountB = new Parameters([
            'type'        => AccountType::ACTIVE,
            'name'        => 'Issue 151 B',
            'description' => 'Account B',
            'accumulate'  => BooleanType::NO,
        ]);
        // Salvar
        $mAccounts->save($accountB);

        // Camada de Modelo
        $mPostings = $serviceLocator->get('Balance\Model\Postings');

        // Lançamento
        $posting = new Parameters([
            'id'          => '',
            'datetime'    => '10/10/2010 10:10:10',
            'description' => 'Issue 151 Posting A',
            'entries'     => [
                [
                    'account_id' => (string) $accountA['id'],
                    'type'       => EntryType::CREDIT,
                    'value'      => '100,00',
                ],
                [
                    'account_id' => (string) $accountB['id'],
                    'type'       => EntryType::DEBIT,
                    'value'      => '100,00',
                ],
            ],
        ]);

        try {
            // Salvar
            $mPostings->save($posting);
        } catch (ModelException $e) {
            // Capturar Formulário
            $form = $mPostings->getForm();
            // Formulário Válido?
            if ($form->isValid()) {
                // Erro em outro lugar!
                throw $e;
            }
            // Capturar Mensagens do Formulário
            $messages = $form->getMessages();
            // Erro Encontrado
            $this->fail('Invalid Form ' . json_encode($messages));
        }
    }
}
