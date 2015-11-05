<?php

namespace Balance\Form;

use Zend\Form\Form;

/**
 * Formulário de Lançamentos
 */
class Postings extends Form
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Chave Primária
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'id',
        ));

        // Data e Hora
        $this->add(array(
            'type' => 'Text',
            'name' => 'datetime',
        ));

        // Descrição
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'description',
        ));
    }
}
