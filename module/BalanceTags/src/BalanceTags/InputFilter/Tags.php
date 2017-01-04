<?php

namespace BalanceTags\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Filter;

/**
 * Filtro de Entrada de Dados para Etiquetas
 */
class Tags extends InputFilter
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Chave Primária
        $input = new Input('id');
        $input->getFilterChain()
            ->attach(new Filter\ToInt());
        $this->add($input);

        // Nome
        $this->add(new Input('name'));
    }
}
