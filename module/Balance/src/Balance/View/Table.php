<?php

namespace Balance\View;

use Closure;

/**
 * Tabela para Renderização de Elementos em Visualização
 */
class Table
{
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
     * @type Closure[]
     */
    protected $actions = array();

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
     * Adicionar Ação
     *
     * @param  string  $identifier Identificador da Ação
     * @param  Closure $callable   Ação para Execução
     * @return Table   Próprio Objeto para Encadeamento
     */
    public function addAction($identifier, Closure $callable)
    {
        $this->actions[$identifier] = $callable;
        return $this;
    }

    /**
     * Apresentação de Ações
     *
     * @return Closure[] Conjunto de Elementos Solicitados
     */
    public function getActions()
    {
        return $this->actions;
    }
}
