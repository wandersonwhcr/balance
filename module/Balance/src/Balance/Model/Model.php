<?php

namespace Balance\Model;

use ArrayAccess;
use Balance\Model\Persistence\PersistenceInterface;
use Traversable;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Form;
use Zend\Stdlib\Parameters;

/**
 * Camada de Modelo
 *
 * Estrutura utilizada como padrão para processamento de informações apresentadas pela camada de controle durante o
 * fluxo de processamento do aplicativo. Centraliza a configuração de objetos de formulário e persistência de dados.
 */
class Model implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * Formulário
     * @type Form
     */
    private $form;

    /**
     * Formulário de Pesquisa
     * @type Form
     */
    private $formSearch;

    /**
     * Persistência de Dados
     * @type PersistenceInterface
     */
    private $persistence;

    /**
     * Construtor Padrão
     *
     * @param PersistenceInterface $persistence Persistência de Dados
     */
    public function __construct(PersistenceInterface $persistence)
    {
        $this->setPersistence($persistence);
    }

    /**
     * Configuração de Formulário
     *
     * @param  Form  $form Elemento para Configuração
     * @return Model Próprio Objeto para Encadeamento
     */
    public function setForm(Form $form)
    {
        // Evento: Configurar Formulário
        $this->getEventManager()->trigger('setForm', $form);
        // Configuração
        $this->form = $form;
        // Encadeamento
        return $this;
    }

    /**
     * Apresentação de Formulário
     *
     * @return Form Elemento Solicitado
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Configuração de Formulário de Pesquisa
     *
     * @param  Form  $formSearch Elemento para Configuração
     * @return Model Próprio Objeto para Encadeamento
     */
    public function setFormSearch(Form $formSearch)
    {
        // Evento: Configurar Formulário de Pesquisa
        $this->getEventManager()->trigger('setFormSearch', $formSearch);
        // Configuração
        $this->formSearch = $formSearch;
        // Encadeamento
        return $this;
    }

    /**
     * Apresentação de Formulário de Pesquisa
     *
     * @return Form Elemento Solicitado
     */
    public function getFormSearch()
    {
        return $this->formSearch;
    }

    /**
     * Configuração de Persistência de Dados
     *
     * @param  PersistenceInterface $persistence Elemento para Configuração
     * @return Model                Próprio Objeto para Encadeamento
     */
    protected function setPersistence(PersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
        return $this;
    }

    /**
     * Apresentação de Persistência de Dados
     *
     * @return PersistenceInterface Elemento Solicitado
     */
    public function getPersistence()
    {
        return $this->persistence;
    }

    /**
     * Consulta de Elementos
     *
     * @param  Parameters  $params Parâmetros de Execução
     * @return Traversable Conjunto de Informações Encontradas
     */
    public function fetch(Parameters $params)
    {
        // Inicialização
        $page = 1;
        // Página Informada?
        if ($params['page']) {
            // Captura
            $page = (int) $params['page'];
        }
        // Formulário de Pesquisa
        $form = $this->getFormSearch();
        // Preencher Formulário
        $form->setData($params);
        // Validação
        $form->isValid();
        // Reiniciar Parâmetros
        $params = new Parameters();
        // Capturar Valores Válidos
        foreach ($form->getInputFilter()->getValidInput() as $identifier => $input) {
            $params[$identifier] = $input->getValue();
        }
        // Parâmetros Adicionais
        $params['page'] = $page;
        // Consulta de Elementos
        $result = $this->getPersistence()->fetch($params);
        // Sucesso?
        if (! $result instanceof Traversable) {
            // Impossível Continuar!
            throw new ModelException('Persistence Result is not Traversable');
        }
        // Apresentação
        return $result;
    }

    /**
     * Carregar Elemento
     *
     * @param  Parameters $params Parâmetros de Execução
     * @return array      Conjunto de Informações Encontradas
     */
    public function load(Parameters $params)
    {
        // Carregar Elementos
        $element = $this->getPersistence()->find($params);
        // Encontrado?
        if (! $element) {
            throw new ModelException('Unknown Element');
        }
        // Sucesso?
        if (! $element instanceof ArrayAccess) {
            // Impossível Continuar
            throw new ModelException('Persistence Result is not Array Accessible');
        }
        // Preencher Formulário
        $this->getForm()->setData($element);
        // Apresentação
        return $element;
    }

    /**
     * Salvar Elemento
     *
     * @param  Parameters     $data Dados para Salvamento
     * @throws ModelException Dados Inválidos
     * @return Model          Próprio Objeto para Encadeamento
     */
    public function save(Parameters $data)
    {
        // Formulário
        $form = $this->getForm();
        // Configurar Dados
        $form->setData($data);
        // Dados Válidos?
        if (! $form->isValid()) {
            throw new ModelException('Invalid Data');
        }
        // Capturar Valores
        $values = $form->getData();
        // Salvar Dados
        $this->getPersistence()->save(new Parameters($values));
        // Encadeamento
        return $this;
    }

    /**
     * Remover Elemento
     *
     * @param  Parameters     $params Parâmetros de Execução
     * @throws ModelException Problema na Remoção do Elemento
     * @return Model          Próprio Objeto para Encadeamento
     */
    public function remove(Parameters $params)
    {
        // Remover Elemento
        $this->getPersistence()->remove($params);
        // Encadeamento
        return $this;
    }
}
