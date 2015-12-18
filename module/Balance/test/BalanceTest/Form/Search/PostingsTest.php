<?php

namespace BalanceTest\Form\Search;

use Balance\Form\Search\Postings;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\FormElementManager;
use Zend\ServiceManager\ServiceManager;

class PostingsTest extends TestCase
{
    public function testInit()
    {
        $form = new Postings();

        // Localizador de Serviços Superior
        $serviceLocator = new ServiceManager();
        // Gerenciador de Formulários
        $formElementManager = new FormElementManager();
        // Dependência
        $formElementManager->setServiceLocator($serviceLocator);
        // Configurar Localizador de Serviços Superior
        $form->setServiceLocator($formElementManager);

        // Camada de Modelo
        $persistence = $this->getMock('Balance\Model\Persistence\ValueOptionsInterface');
        // Carregar Valores
        $persistence->expects($this->atLeastOnce())
            ->method('getValueOptions')
            ->will($this->returnValue([]));

        // Camada de Modelo de Contas
        $serviceLocator->setService('Balance\Model\Persistence\Accounts', $persistence);

        $form->init();

        $this->assertTrue($form->has('keywords'));
        $this->assertInstanceOf('Zend\Form\Element\Text', $form->get('keywords'));

        $this->assertTrue($form->has('account_id'));
        $this->assertInstanceOf('Zend\Form\Element\Select', $form->get('account_id'));

        $this->assertTrue($form->has('datetime_begin'));
        $this->assertInstanceOf('Zend\Form\Element\DateTime', $form->get('datetime_begin'));

        $this->assertTrue($form->has('datetime_end'));
        $this->assertInstanceOf('Zend\Form\Element\DateTime', $form->get('datetime_end'));
    }

    public function testInitWithoutPersistence()
    {
        $this->setExpectedException('Balance\Form\FormException', 'Invalid Model');

        $form = new Postings();

        // Localizador de Serviços Superior
        $serviceLocator = new ServiceManager();
        // Gerenciador de Formulários
        $formElementManager = new FormElementManager();
        // Dependência
        $formElementManager->setServiceLocator($serviceLocator);
        // Configurar Localizador de Serviços Superior
        $form->setServiceLocator($formElementManager);

        // Camada de Modelo de Contas
        $serviceLocator->setService(
            'Balance\Model\Persistence\Accounts',
            $this->getMock('Balance\Model\Persistence\PersistenceInterface')
        );

        $form->init();
    }
}
