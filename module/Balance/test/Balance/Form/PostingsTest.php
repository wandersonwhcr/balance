<?php

namespace Balance\Form;

use Balance\Form\Element\Currency;
use Balance\Model\EntryType;
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
        // Elementos
        $formElementManager->setService('Currency', new Currency());
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

        $this->assertTrue($form->has('id'));
        $this->assertInstanceOf('Zend\Form\Element\Hidden', $form->get('id'));

        $this->assertTrue($form->has('datetime'));
        $this->assertInstanceOf('Zend\Form\Element\DateTime', $form->get('datetime'));

        $this->assertTrue($form->has('description'));
        $this->assertInstanceOf('Zend\Form\Element\Textarea', $form->get('description'));

        $this->assertTrue($form->has('entries'));
        $this->assertInstanceOf('Zend\Form\Element\Collection', $form->get('entries'));
        $this->assertInstanceOf('Zend\Form\Fieldset', $form->get('entries')->getTargetElement());
        $this->assertEquals(2, $form->get('entries')->getCount());

        $subform = $form->get('entries')->getTargetElement();

        $this->assertTrue($subform->has('type'));
        $this->assertInstanceOf('Zend\Form\Element\Select', $subform->get('type'));
        $this->assertEquals((new EntryType())->getDefinition(), $subform->get('type')->getValueOptions());

        $this->assertTrue($subform->has('account_id'));
        $this->assertInstanceOf('Zend\Form\Element\Select', $subform->get('account_id'));

        $this->assertTrue($subform->has('value'));
        $this->assertInstanceOf('Balance\Form\Element\Currency', $subform->get('value'));
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
