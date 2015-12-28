<?php

namespace BalanceTags\Form\Search;

use Zend\Form\Form;

/**
 * Formulário de Pesquisa de Etiquetas
 */
class Tags extends Form
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
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
