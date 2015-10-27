<?php

namespace Balance\Model;

use Zend\Stdlib\Parameters;

/**
 * Estrutura para Persistência de Dados
 */
interface PersistenceInterface
{
    /**
     * Consulta de Elementos
     *
     * @param  Parameters $params Parâmetros de Execução
     * @return array      Conjunto de Informações Encontradas
     */
    public function fetch(Parameters $params);

    /**
     * Salvar Elemento
     *
     * @param  Parameters           $data Dados para Salvamento
     * @return PersistenceInterface Próprio Objeto para Encadeamento
     * @throws ModelException       Problema no Salvamento dos Dados em Persistência
     */
    public function save(Parameters $data);

    /**
     * Remover Elemento
     *
     * @param  Parameters           $params Parâmetros de Execução
     * @return PersistenceInterface Próprio Objeto para Encadeamento
     * @throws ModelException       Problema na Remoção do Elemento
     */
    public function remove(Parameters $params);
}
