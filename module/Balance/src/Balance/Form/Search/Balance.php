<?php

namespace Balance\Form\Search;

use Zend\Form\Form;

/**
 * Formulário de Pesquisa para Balancete
 */
class Balance extends Form
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Data e Hora
        $this->add([
            'type' => 'DateTime',
            'name' => 'datetime',
        ]);
    }
}
