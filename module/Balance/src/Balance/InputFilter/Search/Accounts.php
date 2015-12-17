<?php

namespace Balance\InputFilter\Search;

use Balance\Model\AccountType;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * Filtro de Pesquisa para Contas
 */
class Accounts extends InputFilter
{
    public function init()
    {
        // Tipo
        $input = (new Input('type'))
            ->setRequired(false);
        $input->getValidatorChain()
            ->attach(new Validator\InArray(['haystack' => array_keys((new AccountType())->getDefinition())]));
        $this->add($input);

        // Palavras Chave
        $input = (new Input('keywords'))
            ->setRequired(false);
        $this->add($input);
    }
}
