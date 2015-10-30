<?php

namespace Balance\Entity;

/**
 * Conta
 */
class Account
{
    const TYPE_ACTIVE  = 'ACTIVE';
    const TYPE_PASSIVE = 'PASSIVE';

    /**
     * Apresentação de Definições de Tipos de Conta
     *
     * @return array Conjunto de Informações Solicitadas
     */
    public function getTypeDefinition()
    {
        return array(
            self::TYPE_ACTIVE  => 'Ativo',
            self::TYPE_PASSIVE => 'Passivo',
        );
    }
}
