<?php

namespace Balance\Stdlib;

use InvalidArgumentException;

/**
 * Estrutura para Sincronização de Elementos
 *
 * Classe utilizada para sincronizar dois conjuntos de dados, onde, a partir de colunas para comparação, verifica quais
 * são os elementos que necessitam ser adicionados, atualizados ou excluídos de uma persistência de dados.
 */
class Synchronizer
{
    /**
     * Colunas para Comparação
     * @type string[]
     */
    private $columns = array();

    /**
     * Elementos Antigos
     * @type array
     */
    private $oldElements = array();

    /**
     * Elementos Novos
     * @type array
     */
    private $newElements = array();

    /**
     * Configuração de Colunas para Comparação
     *
     * @param  string[]     $columns Valores para Configuração
     * @return Synchronizer Próprio Objeto para Encadeamento
     */
    public function setColumns(array $columns)
    {
        // Verificar Tipos de Colunas
        foreach ($columns as $column) {
            if (!is_scalar($column)) {
                throw new InvalidArgumentException('Invalid Column');
            }
        }
        // Configurações
        $this->columns = array_values($columns);
        // Encadeamento
        return $this;
    }

    /**
     * Apresentação de Colunas para Comparação
     *
     * @return string[] Valores Configurados
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Configuração de Elementos Antigos para Comparação
     *
     * @param  array        $elements Elementos para Configuração
     * @return Synchronizer Próprio Objeto para Encadeamento
     */
    public function setOldElements(array $elements)
    {
        $this->oldElements = $elements;
        return $this;
    }

    /**
     * Apresentação de Elementos Antigos para Comparação
     *
     * @return array Elementos Configurados
     */
    public function getOldElements()
    {
        return $this->oldElements;
    }

    /**
     * Configuração de Elementos Novos para Comparação
     *
     * @param  array        $elements Elementos para Configuração
     * @return Synchronizer Próprio Objeto para Encadeamento
     */
    public function setNewElements(array $elements)
    {
        $this->newElements = $elements;
        return $this;
    }

    /**
     * Apresentação de Elementos Novos para Comparação
     *
     * @return array Elementos Configurados
     */
    public function getNewElements()
    {
        return $this->newElements;
    }
}
