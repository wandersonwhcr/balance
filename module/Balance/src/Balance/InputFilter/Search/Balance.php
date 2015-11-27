<?php

namespace Balance\InputFilter\Search;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * Filtro de Pesquisa para Contas
 */
class Balance extends InputFilter
{
    public function init()
    {
        // Data e Hora Final
        $input = (new Input('datetime'))
            ->setRequired(false);
        $input->getValidatorChain()
            ->attach(new Validator\Date(array('format' => 'd/m/Y H:i:s')));
        $this->add($input);
    }
}
