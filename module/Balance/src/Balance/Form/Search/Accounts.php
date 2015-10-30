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
        $this->add(array(
            'type'    => 'Select',
            'name'    => 'type',
            'options' => array(
                'label'         => 'Tipo',
                'value_options' => (new AccountType())->getDefinition(),
            ),
        ));

        // Palavras-Chave
        $this->add(array(
            'type'    => 'Text',
            'name'    => 'keywords',
            'options' => array(
                'label' => 'Palavras-Chave',
            ),
        ));
    }
}
