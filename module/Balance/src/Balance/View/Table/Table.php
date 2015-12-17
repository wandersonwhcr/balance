<?php

namespace Balance\View\Table;

use ArrayIterator;
use Traversable;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Tabela para Renderização de Elementos em Visualização
 */
class Table
{
    /**
     * Formulário
     * @type Form
     */
    protected $form = null;

    /**
     * Título da Tabela
     * @type string
     */
    protected $title = '';

    /**
     * Colunas Configuradas
     * @type array[]
     */
    protected $columns = array();

    /**
     * Elementos para Renderização
     * @type Traversable
     */
    protected $elements = null;

    /**
     * Ações Configuradas
     * @type string[][]
     */
    protected $actions = array();

    /**
     * Ações de Elementos Configuradas
     * @type string[][]
     */
    protected $elementActions = array();

    /**
     * Hidratador de Linhas
     * @type HydratorInterface
     */
    protected $hydrator;

    /**
     * Configurar Formulário
     *
     * @param  Form  $form Elemento para Configuração
     * @return Table Próprio Objeto para Encadeamento
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * Apresentar Formulário
     *
     * @return Form Elemento Solicitado
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Configuração de Título da Tabela
     *
     * @param  string $title Valor para Configuração
     * @return Table  Próprio Objeto para Encadeamento
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Apresentação de Título da Tabela
     *
     * @return string Valor Configurado
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Configurar Coluna
     *
     * @param  string $identifier Identificador da Coluna
     * @param  array  $params     Parâmetros para Coluna
     * @return Table  Próprio Objeto para Encadeamento
     */
    public function setColumn($identifier, array $params = array())
    {
        $this->columns[$identifier] = $params;
        return $this;
    }

    /**
     * Apresentação de Colunas
     *
     * @return array[] Conjunto de Elementos Solicitados
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Configuração de Elementos
     *
     * @param  Traversable $elements Conjunto de Elementos
     * @return Table       Próprio Objeto para Encadeamento
     */
    public function setElements(Traversable $elements)
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * Apresentação de Elementos
     *
     * @return Traversable Conjunto de Elementos Solicitados
     */
    public function getElements()
    {
        // Elementos Configurados?
        if ($this->elements === null) {
            // Inicializar Elementos Vazios
            $this->setElements(new ArrayIterator(array()));
        }
        // Apresentação
        return $this->elements;
    }

    /**
     * Configurar Ação
     *
     * @param  string   $identifier Identificador da Ação
     * @param  string[] $params     Parâmetros para Captura em Ação
     * @return Table    Próprio Objeto para Encadeamento
     */
    public function setAction($identifier, array $params)
    {
        $this->actions[$identifier] = $params;
        return $this;
    }

    /**
     * Apresentação de Ações
     *
     * @return string[][] Conjunto de Elementos Solicitados
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Configurar Ação de Elemento
     *
     * @param  string   $identifier Identificador da Ação
     * @param  string[] $params     Parâmetros para Captura em Ação
     * @return Table    Próprio Objeto para Encadeamento
     */
    public function setElementAction($identifier, array $params = array())
    {
        $this->elementActions[$identifier] = $params;
        return $this;
    }

    /**
     * Apresentação de Ações de Elemento
     *
     * @return string[][] Conjunto de Elementos Solicitados
     */
    public function getElementActions()
    {
        return $this->elementActions;
    }

    /**
     * Configuração do Hidratador de Linhas
     *
     * @param  HydratorInterface $hydrator Elemento para Configuração
     * @return Table             Próprio Objeto para Encadeamento
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * Apresentação de Hidratador de Linhas
     *
     * @return HydratorInterface Elemento Solicitado
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }
}
