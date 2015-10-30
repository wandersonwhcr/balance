<?php

namespace Balance\Form\Search;

use Balance\Model\EntryType;
use Zend\Form\Form;

/**
 * Formulário de Pesquisa de Lançamentos
 */
class Postings extends Form
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
                'value_options' => (new EntryType())->getDefinition(),
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
