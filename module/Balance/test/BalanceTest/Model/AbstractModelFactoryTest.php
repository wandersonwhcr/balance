<?php

namespace BalanceTest\Model;

use Balance\Model\AbstractModelFactory;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Form\FormElementManager;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceManager;

class AbstractModelFactoryTest extends TestCase
{
    public function testCreateService()
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

        // Fábrica de Componentes
        $factory = new AbstractModelFactory();
        $result  = $factory->canCreateServiceWithName($serviceLocator, 'model', 'Balance\Model\Model');
        $this->assertTrue($result);
        // Construir Elemento
        $element = $factory->createServiceWithName($serviceLocator, 'table', 'Balance\Model\Model');
        $this->assertInstanceOf('Balance\Model\Model', $element);
        $this->assertSame($form, $element->getForm());
        $this->assertSame($inputFilter, $element->getForm()->getInputFilter());
        $this->assertSame($formSearch, $element->getFormSearch());
        $this->assertSame($inputFilterSearch, $element->getFormSearch()->getInputFilter());
        $this->assertSame($persistence, $element->getPersistence());
    }
}
