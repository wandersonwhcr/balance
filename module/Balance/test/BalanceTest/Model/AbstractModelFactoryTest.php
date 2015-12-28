<?php

namespace BalanceTest\Model;

use Balance\Model\AbstractModelFactory;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManager;
use Zend\Form\Form;
use Zend\Form\FormElementManager;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceManager;

class AbstractModelFactoryTest extends TestCase
{
    protected function setUp()
    {
        // Inicializar Localizador de Serviço
        $serviceLocator = new ServiceManager();

        // Inicialização
        $form              = new Form();
        $inputFilter       = new InputFilter();
        $formSearch        = new Form();
        $inputFilterSearch = new InputFilter();
        $persistence       = $this->getMock('Balance\Model\Persistence\PersistenceInterface');

        // Formulário
        $serviceLocator->setService('Balance\Form\Form', $form);
        // Filtro de Dados
        $serviceLocator->setService('Balance\InputFilter\InputFilter', $inputFilter);
        // Formulário de Pesquisa
        $serviceLocator->setService('Balance\Form\Search\Form', $formSearch);
        // Filtro de Dados de Pesquisa
        $serviceLocator->setService('Balance\InputFilter\Search\InputFilter', $inputFilterSearch);
        // Persistência
        $serviceLocator->setService('Balance\Model\Persistence\Model', $persistence);

        // Gerenciadores
        $serviceLocator
            ->setService('FormElementManager', $serviceLocator)
            ->setService('InputFilterManager', $serviceLocator);

        // Gerenciador de Eventos
        $eventManager = new EventManager();
        // Configuração
        $serviceLocator->setService('EventManager', $eventManager);

        // Configurar Elemento
        $serviceLocator->setService('Config', [
            // Balance
            'balance_manager' => [
                'factories' => [
                    'Balance\Model\Model' => [
                        'factory' => 'Balance\Model\AbstractModelFactory',
                        'params'  => [
                            'form'                => 'Balance\Form\Form',
                            'input_filter'        => 'Balance\InputFilter\InputFilter',
                            'form_search'         => 'Balance\Form\Search\Form',
                            'input_filter_search' => 'Balance\InputFilter\Search\InputFilter',
                            'persistence'         => 'Balance\Model\Persistence\Model',
                        ],
                    ],
                ],
            ],
        ]);

        // Configurações
        $this->serviceLocator    = $serviceLocator;
        $this->eventManager      = $eventManager;
        $this->form              = $form;
        $this->inputFilter       = $inputFilter;
        $this->formSearch        = $formSearch;
        $this->inputFilterSearch = $inputFilterSearch;
        $this->persistence       = $persistence;
    }

    protected function tearDown()
    {
        // Limpeza
        unset($this->serviceLocator);
        unset($this->eventManager);
        unset($this->form);
        unset($this->inputFilter);
        unset($this->formSearch);
        unset($this->inputFilterSearch);
        unset($this->persistence);
    }

    public function testCreateService()
    {
        // Fábrica de Componentes
        $factory = new AbstractModelFactory();
        $result  = $factory->canCreateServiceWithName($this->serviceLocator, 'model', 'Balance\Model\Model');
        $this->assertTrue($result);
        // Construir Elemento
        $element = $factory->createServiceWithName($this->serviceLocator, 'table', 'Balance\Model\Model');
        $this->assertInstanceOf('Balance\Model\Model', $element);
        $this->assertSame($this->form, $element->getForm());
        $this->assertSame($this->inputFilter, $element->getForm()->getInputFilter());
        $this->assertSame($this->formSearch, $element->getFormSearch());
        $this->assertSame($this->inputFilterSearch, $element->getFormSearch()->getInputFilter());
        $this->assertSame($this->persistence, $element->getPersistence());
    }

    public function testTriggerDoCreateForm()
    {
        // Adicionar Atributo: Elemento
        $this->form->setAttribute('role', 'form');
        // Adicionar Atributo: Pesquisa
        $this->formSearch->setAttribute('role', 'search');

        // Evento: Inicializar Formulário (Elemento)
        $this->eventManager->attach('Balance\Model\AbstractModelFactory::doCreateForm', function ($event) {
            // Formulário
            $form = $event->getTarget();
            // Formulário de Elemento?
            if ($form->getAttribute('role') === 'form') {
                // Adicionar Campo de Chave Primária
                $form->add(['type' => 'hidden', 'name' => 'id']);
            }
        });

        // Evento: Inicializar Formulário (Pesquisa)
        $this->eventManager->attach('Balance\Model\AbstractModelFactory::doCreateForm', function ($event) {
            // Formulário
            $form = $event->getTarget();
            // Formulário de Pesquisa?
            if ($form->getAttribute('role') === 'search') {
                // Adicionar Campo de Pesquisa
                $form->add(['type' => 'text', 'name' => 'keywords']);
            }
        });

        // Fábrica de Componentes
        $factory = new AbstractModelFactory();
        // Construir Elemento
        $factory->createServiceWithName($this->serviceLocator, 'model', 'Balance\Model\Model');

        // Verificações
        $this->assertTrue($this->form->has('id'));
        $this->assertFalse($this->form->has('keywords'));
        $this->assertFalse($this->formSearch->has('id'));
        $this->assertTrue($this->formSearch->has('keywords'));
    }
}
