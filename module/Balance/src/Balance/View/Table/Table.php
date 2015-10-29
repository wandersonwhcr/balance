<?php

namespace Balance\View\Table;

/**
 * Tabela para Renderização de Elementos em Visualização
 */
class Table
{
    /**
     * Título da Tabela
     * @type string
     */
    protected $title = '';

    /**
     * Colunas Configuradas
     * @type string[]
     */
    protected $columns = array();

    /**
     * Elementos para Renderização
     * @type mixed
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
     * Adicionar Coluna
     *
     * @param  string $identifier Identificador da Coluna
     * @param  string $column     Nome da Coluna
     * @return Table  Próprio Objeto para Encadeamento
     */
    public function addColumn($identifier, $column)
    {
        $this->columns[$identifier] = $column;
        return $this;
    }

    /**
     * Apresentação de Colunas
     *
     * @return string[] Conjunto de Elementos Solicitados
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Configuração de Elementos
     *
     * @param  mixed $elements Conjunto de Elementos
     * @return Table Próprio Objeto para Encadeamento
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * Apresentação de Elementos
     *
     * @return mixed Conjunto de Elementos Solicitados
     */
    public function getElements()
    {
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
    public function setElementAction($identifier, array $params)
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
}
