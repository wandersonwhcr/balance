<?php

namespace Balance\Form\Search;

use Zend\Form\Form;

/**
 * Formulário de Pesquisa
 */
class Search extends Form
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Palavras-Chave
        $this->add(array(
            'type' => 'Text',
            'name' => 'keywords',
        ));
    }
}
