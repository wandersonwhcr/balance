<?php

namespace BalanceTags\InputFilter\Search;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

/**
 * Filtro de Entrada de Dados de Pesquisa de Etiquetas
 */
class Tags extends InputFilter
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Palavras Chave
        $input = (new Input('keywords'))
            ->setRequired(false);
        $this->add($input);
    }
}
