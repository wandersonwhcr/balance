<?php

namespace BalanceTags\Form;

use Zend\Form\Form;

/**
 * Formulário de Etiquetas
 */
class Tags extends Form
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Chave Primária
        $this->add([
            'type' => 'Hidden',
            'name' => 'id',
        ]);

        // Nome
        $this->add([
            'type' => 'Text',
            'name' => 'name',
        ]);
    }
}
