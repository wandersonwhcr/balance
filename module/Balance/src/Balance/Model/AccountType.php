<?php

namespace Balance\Model;

/**
 * Tipo de Conta
 */
class AccountType
{
    const ACTIVE  = 'ACTIVE';
    const PASSIVE = 'PASSIVE';

    /**
     * Definição de Tipo de Conta
     *
     * @return string[] Definição
     */
    public function getDefinition()
    {
        return [
            self::ACTIVE  => 'Ativo',
            self::PASSIVE => 'Passivo',
        ];
    }
}
