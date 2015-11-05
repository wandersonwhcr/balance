<?php

namespace Balance\Form\Search;

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
        // Palavras-Chave
        $this->add(array(
            'type'    => 'Text',
            'name'    => 'keywords',
            'options' => array(
                'label' => 'Palavras-Chave',
            ),
        ));

        // Data e Hora Inicial
        $this->add(array(
            'type'    => 'Text',
            'name'    => 'datetime_begin',
            'options' => array(
                'label' => 'Data e Hora Inicial',
            ),
        ));

        // Data e Hora Final
        $this->add(array(
            'type'    => 'Text',
            'name'    => 'datetime_end',
            'options' => array(
                'label' => 'Data e Hora Final',
            ),
        ));
    }
}
