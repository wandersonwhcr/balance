<?php

namespace Balance\Form;

use Balance\Model\AccountType;
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
                'value_options' => (new AccountType())->getDefinition(),
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
