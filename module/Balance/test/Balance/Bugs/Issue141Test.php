<?php

namespace Balance\Bugs;

use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use Balance\Model\Persistence\Db\Accounts;
use Balance\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class Issue141Test extends TestCase
{
    public function testAccountUpdateWithException()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Database Error');

        // Inicialização
        $persistence = new Accounts();

        // Gerenciador de Serviços
        $serviceManager = Application::getApplication()->getServiceManager();

        // Localizador de Serviços
        $serviceLocator = new ServiceManager();
        // Configurações
        $persistence->setServiceLocator($serviceLocator);

        // Configurações de Serviços
        $serviceLocator
            ->setService('db', $serviceManager->get('db'))
            ->setService('Balance\Db\TableGateway\Accounts', $serviceManager->get('Balance\Db\TableGateway\Accounts'));

        // Conta para Inserção
        $data = new Parameters(array(
            'type'        => AccountType::ACTIVE,
            'name'        => 'Account A',
            'description' => 'Description',
            'accumulate'  => BooleanType::NO,
        ));

        // Salvar Dados
        $persistence->save($data);

        // Colocar um Tipo Inválido
        $data['type'] = 'UNKNOWN';

        // Salvar Dados
        $persistence->save($data);
    }
}
