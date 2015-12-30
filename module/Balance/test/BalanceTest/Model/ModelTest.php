<?php

namespace BalanceTest\Model;

use ArrayIterator;
use ArrayObject;
use Balance\Model\Model;
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
        $form->add(new Text('id'));
        $inputFilter->add(new Input('id'));
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
        $model = new Model($persistence);
        // Formulários
        $model->setForm($form)->setFormSearch($formSearch);
        // Apresentação
        return $model;
    }

    public function testEventManagerAware()
    {
        // Inicialização
        $model = $this->getModel();
        // Verificações
        $this->assertInstanceOf('Zend\EventManager\EventManagerAwareInterface', $model);
    }

    public function testFetch()
    {
        // Inicialização
        $model   = $this->getModel();
        $dataset = [['one'], ['two']];
        // Camada de Persistência
        $persistence = $model->getPersistence();
        // Mock: Consulta
        $persistence->expects($this->once())->method('fetch')->will($this->returnCallback(function ($params) {
            $result = [];
            if ($params['keywords'] === 'foo bar') {
                $result[] = ['one'];
                $result[] = ['two'];
            }
            return new ArrayIterator($result);
        }));
        // Consulta
        $result = $model->fetch(new Parameters(['keywords' => 'foo bar']));
        // Verificações
        $this->assertInstanceOf('ArrayIterator', $result);
        $this->assertEquals($dataset, $result->getArrayCopy());
    }

    public function testFetchWithoutTraversable()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Persistence Result is not Traversable');
        // Inicialização
        $model = $this->getModel();
        // Camada de Persistência
        $persistence = $model->getPersistence();
        // Mock: Consulta
        $persistence->expects($this->once())->method('fetch')->will($this->returnCallback(function () {
            return [];
        }));
        // Consulta
        $model->fetch(new Parameters());
    }

    public function testLoad()
    {
        // Inicialização
        $model   = $this->getModel();
        $element = new ArrayObject(['foo' => 'bar']);
        // Camada de Persistência
        $persistence = $model->getPersistence();
        // Mock: Carregamento
        $persistence->expects($this->once())->method('find')->will($this->returnCallback(function ($params) {
            $element = [];
            if ($params['id'] === 'foobar') {
                $element['foo'] = 'bar';
            }
            return new ArrayObject($element);
        }));
        // Consulta
        $result = $model->load(new Parameters(['id' => 'foobar']));
        // Verificações
        $this->assertEquals($element, $result);
        $this->assertEquals('bar', $model->getForm()->get('foo')->getValue());
    }

    public function testLoadWithoutArrayAccess()
    {
        // Erro Esperado
        $this->setExpectedException('Balance\Model\ModelException', 'Persistence Result is not Array Accessible');
        // Inicialização
        $model = $this->getModel();
        // Camada de Persistência
        $persistence = $model->getPersistence();
        // Mock: Consulta
        $persistence->expects($this->once())->method('find')->will($this->returnCallback(function () {
            return ['one' => 'two'];
        }));
        // Consulta
        $model->load(new Parameters());
    }

    public function testLoadWithPostLoadEvent()
    {
        // Inicialização
        $model = $this->getModel();

        // Evento: Carregar Elemento com Dados Adicionais
        $model->getEventManager()->attach('Balance\Model\Model::afterLoad', function ($event) {
            // Adicionar Parâmetros
            $event->getTarget()['one'] = 'two';
        });

        // Camada de Persistência
        $persistence = $model->getPersistence();
        // Mock: Consulta
        $persistence->expects($this->once())->method('find')->will($this->returnCallback(function () {
            return new ArrayObject();
        }));

        // Consulta
        $element = $model->load(new Parameters());

        // Verificações
        $this->assertArrayHasKey('one', $element);
        $this->assertEquals('two', $element['one']);
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
        $model->load(new Parameters(['id' => 'foobar']));
    }

    public function testSave()
    {
        // Inicialização
        $model = $this->getModel();
        // Camada de Persistência
        $persistence = $model->getPersistence();
        // Mock: Salvar
        $persistence->expects($this->once())->method('save')->will($this->returnCallback(function ($params) {
            if (! $params['id'] === 'foobar') {
                throw new ModelException('Unknown Element');
            }
            if (! $params['foo'] === 'bar') {
                throw new ModelException('Internal Error');
            }
        }));
        // Consulta
        $result = $model->save(new Parameters(['id' => 'foobar', 'foo' => 'bar']));
        // Verificações
        $this->assertSame($model, $result);
        $this->assertEquals('foobar', $model->getForm()->get('id')->getValue());
        $this->assertEquals('bar', $model->getForm()->get('foo')->getValue());
    }

    public function testSaveWithInvalidData()
    {
        // Expectativas
        $this->setExpectedException('Balance\Model\ModelException', 'Invalid Data');
        // Inicialização
        $model = $this->getModel();
        // Consulta
        $model->save(new Parameters());
    }

    public function testRemove()
    {
        // Inicialização
        $model = $this->getModel();
        // Camada de Persistência
        $persistence = $model->getPersistence();
        // Mock: Remoção
        $persistence->expects($this->once())->method('remove')->will($this->returnCallback(function ($params) {
            if (! $params['id'] === 'foobar') {
                throw new ModelException('Unknown Element');
            }
        }));
        // Remover
        $result = $model->remove(new Parameters(['id' => 'foobar']));
        // Verificações
        $this->assertSame($result, $model);
    }

    public function testTriggerSetForm()
    {
        // Camada de Modelo
        $model = $this->getModel();

        // Formulários
        $form       = $model->getForm();
        $formSearch = $model->getFormSearch();

        // Evento: Inicializar Formulário (Elemento)
        $model->getEventManager()->attach('Balance\Model\Model::setForm', function ($event) {
            // Adicionar Campo de Chave Primária
            $event->getTarget()->add(['type' => 'hidden', 'name' => 'id']);
        });

        // Evento: Inicializar Formulário (Pesquisa)
        $model->getEventManager()->attach('Balance\Model\Model::setFormSearch', function ($event) {
            // Adicionar Campo de Pesquisa
            $event->getTarget()->add(['type' => 'text', 'name' => 'keywords']);
        });

        // Reconfigurar Formulários
        $model
            ->setForm($form)
            ->setFormSearch($formSearch);

        // Verificações
        $this->assertTrue($form->has('id'));
        $this->assertFalse($form->has('keywords'));
        $this->assertFalse($formSearch->has('id'));
        $this->assertTrue($formSearch->has('keywords'));
    }
}
