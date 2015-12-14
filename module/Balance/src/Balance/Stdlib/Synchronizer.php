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
    const INSERT = 'INSERT';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';

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
        return array(
            self::INSERT => array(),
            self::UPDATE => array(),
            self::DELETE => array(),
        );
    }
}
