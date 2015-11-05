<?php

namespace Balance\InputFilter;

use Zend\Filter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * Validação de Dados de Lançamento
 */
class Postings extends InputFilter
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

        // Data e Hora
        $input = new Input('datetime');
        $input->getValidatorChain()
            ->attach(new Validator\Date(array('format' => 'd/m/Y H:i:s')));
        $this->add($input);

        // Descrição
        $this->add(new Input('description'));
    }
}
