<?php

namespace Balance\Form;

use Zend\Form\Form;

/**
 * Formulário de Contas
 */
class Accounts extends Form
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

        // Tipo
        $this->add(array(
            'type'    => 'Select',
            'name'    => 'type',
            'options' => array(
                'value_options' => array(
                    'ACTIVE'  => 'Ativo',
                    'PASSIVE' => 'Passivo',
                ),
            ),
        ));

        // Nome
        $this->add(array(
            'type' => 'Text',
            'name' => 'name',
        ));

        // Descrição
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'description',
        ));
    }
}
