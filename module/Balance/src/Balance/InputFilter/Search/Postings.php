<?php

namespace Balance\InputFilter\Search;

use Zend\Filter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * Filtro de Pesquisa para Contas
 */
class Postings extends InputFilter
{
    public function init()
    {
        // Palavras Chave
        $input = (new Input('keywords'))
            ->setRequired(false);
        $this->add($input);

        // Contas
        $input = (new Input('account_id'))
            ->setRequired(false);
        $input->getFilterChain()
            ->attach(new Filter\ToInt())
            ->attach(new Filter\ToNull());
        $this->add($input);

        // Data e Hora Inicial
        $input = (new Input('datetime_begin'))
            ->setRequired(false);
        $input->getValidatorChain()
            ->attach(new Validator\Date(array('format' => 'd/m/Y H:i:s')));
        $this->add($input);

        // Data e Hora Final
        $input = (new Input('datetime_end'))
            ->setRequired(false);
        $input->getValidatorChain()
            ->attach(new Validator\Date(array('format' => 'd/m/Y H:i:s')));
        $this->add($input);
    }
}
