<?php

namespace Balance\Model;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Parameters;

class ModelTest extends TestCase
{
    protected function getModel()
    {
        // Dependências
        $form              = new Form();
        $inputFilter       = new InputFilter();
        $formSearch        = new Form();
        $inputFilterSearch = new InputFilter();
        $persistence       = $this->getMock('Balance\Model\Persistence\PersistenceInterface');
        // Configurações
        $form->setInputFilter($inputFilter);
        $formSearch->setInputFilter($inputFilterSearch);
        // Pesquisa: Palavras Chave
        $inputFilterSearch->add(new Input('keywords'));
        // Inicialização
        return new Model($form, $formSearch, $persistence);
    }

    public function testFetch()
    {
        // Inicialização
        $model   = $this->getModel();
        $dataset = array(array('one'), array('two'));
        // Camada de Persistência
        $persistence = $model->getPersistence();
        // Mock: Consulta
        $persistence->expects($this->once())->method('fetch')->will($this->returnCallback(function ($params) {
            $result = array();
            if ($params['keywords'] == 'foo bar') {
                $result[] = array('one');
                $result[] = array('two');
            }
            return $result;
        }));
        // Consulta
        $result = $model->fetch(new Parameters(array('keywords' => 'foo bar')));
        // Verificações
        $this->assertEquals($dataset, $result);
    }
}
