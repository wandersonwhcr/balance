<?php

namespace Balance\Model;

/**
 * Tipo Booleano
 */
class BooleanType
{
    const YES = 'YES';
    const NO  = 'NO';

    /**
     * Definição de Tipo Booleano
     *
     * @return string[] Definição
     */
    public function getDefinition()
    {
        return array(
            self::YES => 'Sim',
            self::NO  => 'Não',
        );
    }
}
