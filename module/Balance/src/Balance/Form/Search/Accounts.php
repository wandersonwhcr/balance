<?php

namespace Balance\Form\Search;

use Balance\Model\AccountType;
use Zend\Form\Form;

/**
 * Formulário de Pesquisa de Contas
 */
class Accounts extends Form
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Tipo
        $this->add([
            'type'    => 'Select',
            'name'    => 'type',
            'options' => [
                'label'         => 'Tipo',
                'value_options' => (new AccountType())->getDefinition(),
            ],
        ]);

        // Palavras-Chave
        $this->add([
            'type'    => 'Text',
            'name'    => 'keywords',
            'options' => [
                'label' => 'Palavras-Chave',
            ],
        ]);
    }
}
