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
    // Constantes
    const INSERT    = 'INSERT';
    const UPDATE    = 'UPDATE';
    const DELETE    = 'DELETE';
    const SEPARATOR = '.';

    /**
     * Colunas para Comparação
     * @type string[]
     */
    private $columns = array();

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
            if (! is_scalar($column)) {
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
     * Sincronizar Conjuntos de Dados Antigos e Novos
     *
     * Executa uma sincronização de dados, informando quais valores devem ser utilizados para inserção, atualização e
     * remoção numa possível camada de persistência externa.
     *
     * @param  array $old Conjunto de Dados Entigos
     * @param  array $new Conjunto de Dados Novos
     * @return array Conjuntos de Dados para Sincronização
     */
    public function synchronize(array $old, array $new)
    {
        // Resultado Inicial
        $result = array(
            self::INSERT => array(),
            self::UPDATE => array(),
            self::DELETE => array(),
        );

        // Inicialização
        $columns = $this->getColumns();

        // Processar Conjunto Antigo
        $oldElements = array();
        foreach ($old as $element) {
            // Capturar Valores de Colunas
            $values = array_intersect_key($element, array_flip($columns));
            // Criação de Hash
            $hash = md5(implode(self::SEPARATOR, $values));
            // Configuração
            $oldElements[$hash] = $element;
        }

        // Processar Conjunto Novo
        $newElements = array();
        foreach ($new as $element) {
            // Capturar Valores de Colunas
            $values = array_intersect_key($element, array_flip($columns));
            // Criação de Hash
            $hash = md5(implode(self::SEPARATOR, $values));
            // Configuração
            $newElements[$hash] = $element;
        }

        // Quais Elementos para Inserção?
        $result[self::INSERT] = array_values(array_diff_key($newElements, $oldElements));
        // Quais Elementos para Atualização?
        $result[self::UPDATE] = array_values(array_intersect_key($newElements, $oldElements));
        // Quais Elementos para Remoção?
        $result[self::DELETE] = array_values(array_diff_key($oldElements, $newElements));

        // Apresentar Resultado
        return $result;
    }
}
