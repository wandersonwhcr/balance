<?php

namespace Balance\InputFilter;

use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use Zend\Filter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * Validação de Dados de Conta
 */
class Accounts extends InputFilter
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

        // Tipo
        $input = new Input('type');
        $input->getValidatorChain()
            ->attach(new Validator\InArray(['haystack' => array_keys((new AccountType())->getDefinition())]));
        $this->add($input);

        // Acúmulo de Conta
        $input = new Input('accumulate');
        $input->getValidatorChain()
            ->attach(new Validator\InArray(['haystack' => array_keys((new BooleanType())->getDefinition())]));
        $this->add($input);

        // Nome
        $this->add(new Input('name'));

        // Descrição
        $this->add(new Input('description'));
    }
}
