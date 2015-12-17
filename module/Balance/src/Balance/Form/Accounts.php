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
        $this->add([
            'type' => 'Hidden',
            'name' => 'id',
        ]);

        // Tipo
        $this->add([
            'type'    => 'Select',
            'name'    => 'type',
            'options' => [
                'value_options' => (new AccountType())->getDefinition(),
            ],
        ]);

        // Acumular Valores?
        $this->add([
            'type' => 'Boolean',
            'name' => 'accumulate',
        ]);

        // Nome
        $this->add([
            'type' => 'Text',
            'name' => 'name',
        ]);

        // Descrição
        $this->add([
            'type' => 'Textarea',
            'name' => 'description',
        ]);
    }
}
