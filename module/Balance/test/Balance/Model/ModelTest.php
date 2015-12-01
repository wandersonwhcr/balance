<?php

namespace Balance\Model;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Text;
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
        // Parâmetro
        $form->add(new Text('foo'));
        $inputFilter->add(new Input('foo'));
        // Pesquisa: Palavras Chave
        $formSearch->add(new Text('keywords'));
        $inputFilterSearch->add(new Input('keywords'));
        // Configurações
        $form->setInputFilter($inputFilter);
        $formSearch->setInputFilter($inputFilterSearch);
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

    public function testLoad()
    {
        // Inicialização
        $model   = $this->getModel();
        $element = array('foo' => 'bar');
        // Camada de Persistência
        $persistence = $model->getPersistence();
        // Mock: Carregamento
        $persistence->expects($this->once())->method('find')->will($this->returnCallback(function ($params) {
            $element = array();
            if ($params['id'] == 'foobar') {
                $element['foo'] = 'bar';
            }
            return $element;
        }));
        // Consulta
        $result = $model->load(new Parameters(array('id' => 'foobar')));
        // Verificações
        $this->assertEquals($element, $result);
        $this->assertEquals('bar', $model->getForm()->get('foo')->getValue());
    }

    public function testLoadWithUnknownElement()
    {
        // Expectativas
        $this->setExpectedException('Balance\Model\ModelException', 'Unknown Element');
        // Inicialização
        $model = $this->getModel();
        // Camada de Persistência
        $persistence = $model->getPersistence();
        // Mock: Carregamento
        $persistence->expects($this->once())->method('find')->will($this->returnValue(false));
        // Consulta
        $model->load(new Parameters(array('id' => 'foobar')));
    }
}
