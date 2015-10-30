<?php

namespace Balance\Model;

/**
 * Tipo de Entrada de Lançamento
 */
class EntryType
{
    const CREDIT = 'CREDIT';
    const DEBIT  = 'DEBIT';

    /**
     * Definição de Tipo de Entrada de Lançamento
     *
     * @return string[] Definição
     */
    public function getDefinition()
    {
        return array(
            self::CREDIT => 'Crédito',
            self::DEBIT  => 'Débito',
        );
    }
}

