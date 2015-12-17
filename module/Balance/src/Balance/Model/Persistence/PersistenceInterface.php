<?php

namespace Balance\Model\Persistence;

use Zend\Stdlib\Parameters;

/**
 * Estrutura para Persistência de Dados
 */
interface PersistenceInterface
{
    /**
     * Consulta de Elementos
     *
     * @param  Parameters  $params Parâmetros de Execução
     * @return Traversable Conjunto de Informações Encontradas
     */
    public function fetch(Parameters $params);

    /**
     * Consultar Elemento
     *
     * @param  Parameters $params Parâmetros de Execução
     * @return array      Informações Encontradas
     */
    public function find(Parameters $params);

    /**
     * Salvar Elemento
     *
     * @param  Parameters           $data Dados para Salvamento
     * @throws ModelException       Problema no Salvamento dos Dados em Persistência
     * @return PersistenceInterface Próprio Objeto para Encadeamento
     */
    public function save(Parameters $data);

    /**
     * Remover Elemento
     *
     * @param  Parameters           $params Parâmetros de Execução
     * @throws ModelException       Problema na Remoção do Elemento
     * @return PersistenceInterface Próprio Objeto para Encadeamento
     */
    public function remove(Parameters $params);
}
