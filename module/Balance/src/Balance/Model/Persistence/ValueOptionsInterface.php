<?php

namespace Balance\Model\Persistence;

/**
 * Opções para Valores
 *
 * Estrutura utilizada para confirmar o método responsável por apresentar as opções de valores na camada de
 * persistência. Esta interface é usada em formulários para preencher campos mapeados com nome e valor, como Select e
 * Checkboxes.
 */
interface ValueOptionsInterface
{
    /**
     * Apresentação de Opções de Valores
     *
     * @return array Conjunto de Valores Solicitados
     */
    public function getValueOptions();
}
